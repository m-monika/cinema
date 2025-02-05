<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Rules\Rule;

class SpaceBetweenRequestedSeats implements Rule
{
    /**
     * @var int
     */
    private $spaceBetweenSeats;

    /**
     * @param int $spaceBetweenSeats
     */
    public function __construct(int $spaceBetweenSeats)
    {
        $this->spaceBetweenSeats = $spaceBetweenSeats;
    }

    public function canMakeReservation(RequestedSeat ...$requestedSeats): bool
    {
        $groupedSeats = $this->groupSeats($requestedSeats);

        return $this->checkGroupedSeats($groupedSeats);
    }

    private function groupSeats(array $requestedSeats): array
    {
        $groupedSeats = [];

        foreach ($requestedSeats as $requestedSeat) {
            $sector = $requestedSeat->getSector();
            $row = $requestedSeat->getRow();
            $groupedSeats[$sector][$row][] = $requestedSeat->getSeatInRow();
        }

        return $groupedSeats;
    }

    private function checkGroupedSeats(array $groupedSeats): bool
    {
        foreach ($groupedSeats as $rows) {
            if (!$this->checkRowsInSector($rows)) {
                return false;
            }
        }

        return true;
    }

    private function checkRowsInSector(array $rows): bool
    {
        foreach ($rows as $seatsInRow) {
            if (!$this->checkSeatsInRow($seatsInRow)) {
                return false;
            }
        }

        return true;
    }

    private function checkSeatsInRow(array $seatsInRow): bool
    {
        $previousSeatInRow = null;

        foreach ($seatsInRow as $seatInRow) {
            if (!$this->checkIsSpaceBetweenSeats($seatInRow, $previousSeatInRow)) {
                return false;
            }

            $previousSeatInRow = $seatInRow;
        }

        return true;
    }

    private function checkIsSpaceBetweenSeats(
        int $seatInRow,
        ?int $previousSeatInRow
    ): bool {
        return ($previousSeatInRow === null
            || ($seatInRow - $previousSeatInRow === 1)
            || ($seatInRow - $previousSeatInRow > $this->spaceBetweenSeats)
        );
    }
}
