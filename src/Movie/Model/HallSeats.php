<?php
declare(strict_types = 1);

namespace Cinema\Movie\Model;

class HallSeats
{
    /**
     * @var array<int,array<int,array<int,Seat>>> ['sector' => ['row' => ['seatInRow' => Seat]]]
     */
    private $seats;

    /**
     * @param Seat ...$seats
     */
    public function __construct(Seat ...$seats)
    {
        $this->seats = [];

        foreach ($seats as $seat) {
            $sector = $seat->getSector();
            $row = $seat->getRow();
            $seatInRow = $seat->getSeatInRow();
            $this->seats[$sector][$row][$seatInRow] = $seat;
        }
    }

    /**
     * @param int $sector
     * @param int $row
     * @param int $seatInRow
     *
     * @return bool
     */
    public function isSeatAvailable(int $sector, int $row, int $seatInRow): bool
    {
        if (!$this->seatExists($sector, $row, $seatInRow)) {
            return false;
        }

        return ($this->seats[$sector][$row][$seatInRow])->isAvailable();
    }

    private function seatExists(int $sector, int $row, int $seatInRow): bool
    {
        return $this->sectorExists($sector)
            && $this->rowExists($sector, $row)
            && \array_key_exists($seatInRow, $this->seats[$sector][$row]);
    }

    private function sectorExists(int $sector): bool
    {
        return \array_key_exists($sector, $this->seats);
    }

    private function rowExists(int $sector, int $row): bool
    {
        return \array_key_exists($row, $this->seats[$sector]);
    }

    /**
     * @param int $sector
     * @param int $row
     * @param int $seatInRow
     *
     * @return bool
     */
    public function reserveSeat(int $sector, int $row, int $seatInRow): bool
    {
        return ($this->seats[$sector][$row][$seatInRow])->reserveSeat();
    }
}
