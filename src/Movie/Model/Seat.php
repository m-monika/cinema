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
     * @var int
     */
    private $sector;

    /**
     * @var int
     */
    private $version;

    /**
     * @param int $sector
     * @param int $row
     * @param int $seatInRow
     * @param bool $isAvailable
     * @param int $version
     */
    public function __construct(
        int $sector,
        int $row,
        int $seatInRow,
        bool $isAvailable,
        int $version = 0
    ) {
        $this->sector = $sector;
        $this->row = $row;
        $this->seatInRow = $seatInRow;
        $this->isAvailable = $isAvailable;
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getSector(): int
    {
        return $this->sector;
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

    /**
     * @return bool
     */
    public function reserveSeat(): bool
    {
        if ($this->isAvailable) {
            $this->isAvailable = false;

            return true;
        }

        return false;
    }
}
