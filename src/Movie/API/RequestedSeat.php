<?php
declare(strict_types = 1);

namespace Cinema\Movie\API;

class RequestedSeat
{
    /**
     * @var int
     */
    private $sector;

    /**
     * @var int
     */
    private $row;

    /**
     * @var int
     */
    private $seatInRow;

    /**
     * @param int $sector
     * @param int $row
     * @param int $seatInRow
     */
    public function __construct(int $sector, int $row, int $seatInRow)
    {
        $this->sector = $sector;
        $this->row = $row;
        $this->seatInRow = $seatInRow;
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
}
