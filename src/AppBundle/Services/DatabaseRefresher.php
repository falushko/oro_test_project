<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class DatabaseRefresher
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateLogRecordsWithNewLogs()
    {

    }
}