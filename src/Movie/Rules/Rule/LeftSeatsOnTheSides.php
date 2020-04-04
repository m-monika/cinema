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
     * @param RequestedSeat ...$requestedSeats
     */
    public function __construct(
        int $seatOnSideToLeave,
        Model\HallSeats $hallSeats,
        RequestedSeat ...$requestedSeats
    ) {
        $this->seatOnSideToLeave = $seatOnSideToLeave;
        $this->hallSeats = $hallSeats;
        foreach ($requestedSeats as $requestedSeat) {
            $this->requestedSeats[$requestedSeat->getRow()][]
                = $requestedSeat->getSeatInRow();
        }
    }

    public function canUse(): bool
    {
        return true;
    }

    public function canMakeReservation(): bool
    {
        foreach ($this->requestedSeats as $row => $seatsInRow) {
            $previousSeatInRow = null;
            foreach ($seatsInRow as $seatInRow) {
                if (!$this->canTakeThisSeat($row, $seatInRow, $previousSeatInRow)) {
                    return false;
                }

                $previousSeatInRow = $seatInRow;
            }
        }

        return true;
    }

    private function canTakeThisSeat(
        int $row,
        int $seatInRow,
        ?int $previousSeatInRow
    ): bool {
        return $this->checkIsSpaceBetweenSeats($seatInRow, $previousSeatInRow)
            && $this->checkLeftSideOnSeat($row, $seatInRow)
            && $this->checkRightSideOnSeat($row, $seatInRow);
    }

    private function checkIsSpaceBetweenSeats(
        int $seatInRow,
        ?int $previousSeatInRow
    ): bool {
        return ($previousSeatInRow === null
            || ($seatInRow - $previousSeatInRow === 1)
            || ($seatInRow - $previousSeatInRow > $this->seatOnSideToLeave)
        );
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
