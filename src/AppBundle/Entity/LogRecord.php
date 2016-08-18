<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ValidationTrait;

class LogRecord
{
    use ValidationTrait;

    private $id;
    private $datetime;
    private $record;
    private $fullRecord;

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    public function getRecord()
    {
        return $this->record;
    }

    public function setRecord($record)
    {
        $this->record = $record;
    }

    public function setFullRecord($fullRecord)
    {
        $this->fullRecord = $fullRecord;
    }
}