<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ValidationTrait;

class LogRecord
{
    use ValidationTrait;

    private $id;
    private $datetime;
    private $record;
}