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
     * @var int[][]
     */
    private $requestedSeats = [];

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
        $this->mapRequestedSeats($requestedSeats);

        foreach ($this->requestedSeats as $row => $seatsInRow) {
            foreach ($seatsInRow as $seatInRow) {
                if (!$this->canTakeThisSeat($row, $seatInRow)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function mapRequestedSeats(array $requestedSeats): void
    {
        foreach ($requestedSeats as $requestedSeat) {
            $this->requestedSeats[$requestedSeat->getRow()][]
                = $requestedSeat->getSeatInRow();
        }
    }

    private function canTakeThisSeat(
        int $row,
        int $seatInRow
    ): bool {
        return $this->checkLeftSideOnSeat($row, $seatInRow)
            && $this->checkRightSideOnSeat($row, $seatInRow);
    }

    private function checkLeftSideOnSeat(
        int $row,
        int $seatInRow
    ): bool {
        if ($this->noSeatsOnLeft($seatInRow - 1)) {
            return true;
        }

        for ($seat = 1; $seat <= $this->seatOnSideToLeave; $seat++) {
            $seatToCheckOnLeft = $seatInRow - $seat;

            if ($this->noSeatsOnLeft($seatToCheckOnLeft)) {
                if ($seatToCheckOnLeft >= $this->seatOnSideToLeave) {
                    return true;
                } else {
                    return false;
                }
            }

            if (!$this->hallSeats->isSeatAvailable($row, $seatToCheckOnLeft)) {
                return false;
            }
        }

        return true;
    }

    private function checkRightSideOnSeat(
        int $row,
        int $seatInRow
    ): bool {
        if ($this->noSeatsOnRight($seatInRow + 1, $row)) {
            return true;
        }

        for ($seat = 1; $seat <= $this->seatOnSideToLeave; $seat++) {
            $seatToCheckOnRight = $seatInRow + $seat;

            if ($this->noSeatsOnRight($seatToCheckOnRight, $row)) {
                if (($seatToCheckOnRight - $this->seatOnSideToLeave) == $seatInRow) {
                    return false;
                } else {
                    return true;
                }
            }

            if (!$this->hallSeats->isSeatAvailable($row, $seatToCheckOnRight)) {
                return false;
            }
        }

        return true;
    }

    private function noSeatsOnLeft(int $seatInRow): bool
    {
        return $seatInRow < 1;
    }

    private function noSeatsOnRight(int $seatInRow, int $row): bool
    {
        return $seatInRow > $this->hallSeats->maxSeatsInRow($row);
    }
}
