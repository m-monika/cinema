<?php
declare(strict_types = 1);

namespace Cinema\Movie\API;

class RequestedSeat
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
     * @param int $row
     * @param int $seatInRow
     */
    public function __construct(int $row, int $seatInRow)
    {
        $this->row = $row;
        $this->seatInRow = $seatInRow;
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
