<?php
declare(strict_types = 1);

namespace Cinema\Movie\API;

use Webmozart\Assert\Assert;

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
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $sector, int $row, int $seatInRow)
    {
        Assert::greaterThan($sector, 0, 'The sector number must be greater than 0.');
        Assert::greaterThan($row, 0, 'The row number must be greater than 0.');
        Assert::greaterThan($seatInRow, 0, 'The seat in row number must be greater than 0.');

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
