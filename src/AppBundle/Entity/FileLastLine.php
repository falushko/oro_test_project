<?php

namespace AppBundle\Entity;

/**
 * Class FileLastLine
 * @package AppBundle\Entity
 * Entity for storing last log file lines.
 * Needed to update database with only new lines.
 */
class FileLastLine
{
    private $id;
    private $fileName;
    private $line;

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param mixed $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }


}