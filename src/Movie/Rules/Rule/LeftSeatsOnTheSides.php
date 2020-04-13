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

        foreach ($requestedSeats as $row => $seatsInRow) {
            if (!$this->checkSeatsInRow($row, $seatsInRow)) {
                return false;
            }
        }

        return true;
    }

    private function groupSeatsByRow(array $requestedSeats): array
    {
        $result = [];

        foreach ($requestedSeats as $requestedSeat) {
            $result[$requestedSeat->getRow()][] = $requestedSeat->getSeatInRow();
        }

        return $result;
    }

    private function checkSeatsInRow(int $row, array $seatsInRow): bool
    {
        foreach ($seatsInRow as $seatInRow) {
            if (!$this->canTakeThisSeat($row, $seatInRow)) {
                return false;
            }
        }

        return true;
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
        if (!$this->hallSeats->isSeatAvailable($row, $seatInRow - 1)) {
            return true;
        }

        for ($seatCount = 2; $seatCount <= $this->seatOnSideToLeave; $seatCount++) {
            $seatToCheckOnLeft = $seatInRow - $seatCount;

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
        if (!$this->hallSeats->isSeatAvailable($row, $seatInRow + 1)) {
            return true;
        }

        for ($seatCount = 2; $seatCount <= $this->seatOnSideToLeave; $seatCount++) {
            $seatToCheckOnLeft = $seatInRow + $seatCount;

            if (!$this->hallSeats->isSeatAvailable($row, $seatToCheckOnLeft)) {
                return false;
            }
        }

        return true;
    }
}
