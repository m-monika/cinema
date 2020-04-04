<?php
declare(strict_types = 1);

namespace Cinema\Movie;

abstract class Result
{
    /**
     * @var string|null
     */
    protected $reason;

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this instanceof Result\Success;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return $this instanceof Result\Failure;
    }

    /**
     * @return string|null
     */
    public function failureReason(): ?string
    {
        return $this->isFailure() ? $this->reason : null;
    }
}
