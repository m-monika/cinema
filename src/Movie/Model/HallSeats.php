<?php
declare(strict_types = 1);

namespace Cinema\Movie\Model;

class HallSeats
{
    /**
     * @var array<int,array<int,Seat>> ['row' => ['seatInRow' => Seat]]
     */
    private $seats;

    /**
     * @param Seat ...$seats
     */
    public function __construct(Seat ...$seats)
    {
        $this->seats = [];

        foreach ($seats as $seat) {
            $this->seats[$seat->getRow()][$seat->getSeatInRow()] = $seat;
        }
    }

    /**
     * @param int $row
     * @param int $seatInRow
     *
     * @return bool
     */
    public function isSeatAvailable(int $row, int $seatInRow): bool
    {
        if (!$this->seatExists($row, $seatInRow)) {
            throw new \InvalidArgumentException("Hall does not have this seat");
        }

        return ($this->seats[$row][$seatInRow])->isAvailable();
    }

    private function seatExists(int $row, int $seatInRow): bool
    {
        return $this->rowExists($row)
            && \array_key_exists($seatInRow, $this->seats[$row]);
    }

    /**
     * @param int $row
     *
     * @return int
     */
    public function maxSeatsInRow(int $row): int
    {
        if (!$this->rowExists($row)) {
            throw new \InvalidArgumentException("Hall does not have this row");
        }

        return \count($this->seats[$row]);
    }

    private function rowExists(int $row): bool
    {
        return \array_key_exists($row, $this->seats);
    }

    /**
     * @param int $row
     * @param int $seatInRow
     *
     * @return bool
     */
    public function reserveSeat(int $row, int $seatInRow): bool
    {
        return ($this->seats[$row][$seatInRow])->reserveSeat();
    }
}
