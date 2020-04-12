<?php
declare(strict_types = 1);

namespace Cinema\Movie\Model;

use Cinema\Movie\API;
use Cinema\Movie\Rules\Rule;

class Screening
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var HallSeats
     */
    private $hallSeats;

    /**
     * @param int $id
     * @param HallSeats $hallSeats
     */
    public function __construct(int $id, HallSeats $hallSeats)
    {
        $this->id = $id;
        $this->hallSeats = $hallSeats;
    }

    /**
     * @param Rule|null $rule
     * @param API\RequestedSeat $requestedSeats
     *
     * @return bool
     */
    public function makeReservation(
        ?Rule $rule,
        API\RequestedSeat ...$requestedSeats
    ): bool {
        foreach ($requestedSeats as $requestedSeat) {
            $row = $requestedSeat->getRow();
            $seatInRow = $requestedSeat->getSeatInRow();

            if (!$this->hallSeats->isSeatAvailable($row, $seatInRow)) {
                return false;
            }

            $this->hallSeats->reserveSeat($row, $seatInRow);
        }

        if ($rule instanceof Rule) {
            return $rule->canMakeReservation();
        }

        return true;
    }
}
