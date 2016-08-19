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

            if ($file instanceof SplFileInfo); //to enable autocomplete

            if ($file->isDir()) continue;

            $file = $file->openFile();
            $fileLastLine = $this->getFileLastLine($file);

            while (!$file->eof()) {

                $result = [];
                $matched = preg_match(self::LOG_PATTERN, $file->fgets(), $result);

                if (!$matched) continue;

                $logRecord = new LogRecord($result);

                if (!$logRecord->isValid($this->validatorInterface, 'new')) continue;

                $this->entityManager->persist($logRecord);
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