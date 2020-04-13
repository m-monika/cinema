<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model;
use Cinema\Movie\Rules\Rule;

class LeftSeatsOnTheSides implements Rule
{
    /**
     * @var Model\HallSeats
     */
    private $hallSeats;

    /**
     * @var int
     */
    private $seatOnSideToLeave;

    /**
     * @param int $seatOnSideToLeave
     * @param Model\HallSeats $hallSeats
     */
    public function __construct(
        int $seatOnSideToLeave,
        Model\HallSeats $hallSeats
    ) {
        $this->seatOnSideToLeave = $seatOnSideToLeave;
        $this->hallSeats = $hallSeats;
    }

    public function canMakeReservation(RequestedSeat ...$requestedSeats): bool
    {
        $requestedSeats = $this->groupSeatsByRow($requestedSeats);

        foreach ($requestedSeats as $sector => $rows) {
            if (!$this->checkRowsInSector($sector, $rows)) {
                return false;
            }
        }

        return true;
    }

    private function groupSeatsByRow(array $requestedSeats): array
    {
        $result = [];

        foreach ($requestedSeats as $requestedSeat) {
            $sector = $requestedSeat->getSector();
            $row = $requestedSeat->getRow();
            $result[$sector][$row][] = $requestedSeat->getSeatInRow();
        }

        return $result;
    }

    private function checkRowsInSector(int $sector, array $rows): bool
    {
        foreach ($rows as $row => $seatsInRow) {
            if (!$this->checkSeatsInRow($sector, $row, $seatsInRow)) {
                return false;
            }
        }

        return true;
    }

    private function checkSeatsInRow(int $sector, int $row, array $seatsInRow): bool
    {
        foreach ($seatsInRow as $seatInRow) {
            if (!$this->canTakeThisSeat($sector, $row, $seatInRow)) {
                return false;
            }
        }

        return true;
    }

    private function canTakeThisSeat(
        int $sector,
        int $row,
        int $seatInRow
    ): bool {
        return $this->checkLeftSideOnSeat($sector, $row, $seatInRow)
            && $this->checkRightSideOnSeat($sector, $row, $seatInRow);
    }

    private function checkLeftSideOnSeat(
        int $sector,
        int $row,
        int $seatInRow
    ): bool {
        if (!$this->hallSeats->isSeatAvailable($sector, $row, $seatInRow - 1)) {
            return true;
        }

        for ($seatCount = 2; $seatCount <= $this->seatOnSideToLeave; $seatCount++) {
            $seatToCheckOnLeft = $seatInRow - $seatCount;

            if (!$this->hallSeats->isSeatAvailable($sector, $row, $seatToCheckOnLeft)) {
                return false;
            }
        }

        return true;
    }

    private function checkRightSideOnSeat(
        int $sector,
        int $row,
        int $seatInRow
    ): bool {
        if (!$this->hallSeats->isSeatAvailable($sector, $row, $seatInRow + 1)) {
            return true;
        }

        for ($seatCount = 2; $seatCount <= $this->seatOnSideToLeave; $seatCount++) {
            $seatToCheckOnLeft = $seatInRow + $seatCount;

            if (!$this->hallSeats->isSeatAvailable($sector, $row, $seatToCheckOnLeft)) {
                return false;
            }
        }

        return true;
    }
}
