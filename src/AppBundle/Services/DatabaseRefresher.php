<?php

namespace AppBundle\Services;

use AppBundle\Entity\FileLastLine;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DatabaseRefresher
{
    const LOG_PATTERN = '/([\s\S]+) - - (\[[\s\S]{26}\]) ([\s\S]+)/';

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateLogRecordsWithNewLogs()
    {
        $finder = new Finder();
        $finder->files()->in('/vagrant/logs/');

        foreach ($finder as $file) {

            if ($file instanceof SplFileInfo);

            if ($file->isDir()) continue;

            $openedFile = $file->openFile();
            $fileName = $openedFile->getBasename();

            $fileLastLine = $this->entityManager
                ->getRepository('AppBundle:FileLastLine')
                ->findOneBy(['fileName' => $fileName]);

            if (empty($fileLastLine)) {
                $fileLastLine = new FileLastLine();
                $fileLastLine->setFileName($fileName);
            } else {
                $openedFile->seek($fileLastLine->getLine() - 1);
            }

            while (!$openedFile->eof()) {

                //todo make all magic here

                $result = [];

                preg_match(self::LOG_PATTERN, $openedFile->fgets(), $result);

                // [07/Mar/2004:22:45:46 -0800]

                $dateTime = \DateTime::createFromFormat('d#M#Y#H#i#s T', $result[2]);


                dump($dateTime); exit();

                echo $openedFile->key() + 2 . ' ' . $openedFile->fgets();
            }

            $fileLastLine->setLine($openedFile->key() + 1);
            $this->entityManager->persist($fileLastLine);
            $this->entityManager->flush();

            echo "\r\n";
        }
    }
}