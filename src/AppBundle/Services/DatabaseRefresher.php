<?php

namespace AppBundle\Services;

use AppBundle\Entity\FileLastLine;
use AppBundle\Entity\LogRecord;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DatabaseRefresher
{
    const LOG_PATTERN = '/([\s\S]+) - - (\[[\s\S]{26}\]) ([\s\S]+)/';

    private $entityManager;
    private $validatorInterface;

    public function __construct(EntityManager $entityManager, ValidatorInterface $validatorInterface)
    {
        $this->entityManager = $entityManager;
        $this->validatorInterface = $validatorInterface;
    }

    public function updateLogRecordsWithNewLogs()
    {
        $finder = new Finder();
        $finder->files()->in('/vagrant/logs/');

        foreach ($finder as $file) {

            if ($file instanceof SplFileInfo);

            if ($file->isDir()) continue;

            $file = $file->openFile();

            $fileLastLine = $this->getFileLastLine($file);

            while (!$file->eof()) {

                $result = [];

                $matched = preg_match(self::LOG_PATTERN, $file->fgets(), $result);

                if (!$matched) continue;

                $logRecord = new LogRecord();
                $logRecord->setDatetime(\DateTime::createFromFormat('d#M#Y#H#i#s O', trim($result[2], '[]')));
                $logRecord->setRecord($result[1] . ' - ' . $result[3]);
                $logRecord->setFullRecord($result[0]);


                if (!$logRecord->isValid($this->validatorInterface, 'new')) {
                    die('123');
                };
            }

            $fileLastLine->setLine($file->key() + 1);
            $this->entityManager->persist($fileLastLine);
            $this->entityManager->flush();
        }
    }

    /**
     * @param \SplFileObject $file
     * @return FileLastLine
     * Get FileLastLine if already exists in database. If not - create new.
     */
    private function getFileLastLine(\SplFileObject $file)
    {
        $fileName = $file->getBasename();

        $fileLastLine = $this->entityManager
            ->getRepository('AppBundle:FileLastLine')
            ->findOneBy(['fileName' => $fileName]);

        if (empty($fileLastLine)) {
            $fileLastLine = new FileLastLine();
            $fileLastLine->setFileName($fileName);
        } else {
            $file->seek($fileLastLine->getLine() - 1);
        }

        return $fileLastLine;
    }
}