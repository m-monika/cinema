<?php
declare(strict_types = 1);

namespace Cinema\Movie\Model;

class Seat
{
    /**
     * @var int
     */
    private $row;

    /**
     * @var int
     */
    private $seatInRow;

    /**
     * @var bool
     */
    private $isAvailable;

    /**
     * @param int $row
     * @param int $seatInRow
     * @param bool $isAvailable
     */
    public function __construct(int $row, int $seatInRow, bool $isAvailable)
    {
        $this->row = $row;
        $this->seatInRow = $seatInRow;
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getSeatInRow(): int
    {
        return $this->seatInRow;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function reserveSeat(): void
    {
        $this->isAvailable = false;
    }
}
