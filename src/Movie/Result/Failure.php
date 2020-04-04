<?php
declare(strict_types = 1);

namespace Cinema\Movie\Result;

use Cinema\Movie\Result;

class Failure extends Result
{
    /**
     * @var string
     */
    protected $reason;

    /**
     * @param string $reason
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }
}
