<?php
declare(strict_types = 1);

namespace Cinema\Movie\Model;

use Cinema\Movie\API;
use Cinema\Movie\Rules\Rule;

class Reservation
{
    /**
     * @var int
     */
    private $idScreening;

    /**
     * @var HallSeats
     */
    private $hallSeats;

    /**
     * @param int $idScreening
     * @param HallSeats $hallSeats
     */
    public function __construct(int $idScreening, HallSeats $hallSeats)
    {
        $this->idScreening = $idScreening;
        $this->hallSeats = $hallSeats;
    }

    /**
     * @param Rule $rule
     * @param API\RequestedSeat $requestedSeats
     *
     * @return bool
     */
    public function make(
        Rule $rule,
        API\RequestedSeat ...$requestedSeats
    ): bool {
        foreach ($requestedSeats as $requestedSeat) {
            if (!$this->reserveSeat($requestedSeat)) {
                return false;
            }
        }

        return $rule->canMakeReservation(...$requestedSeats);
    }

    private function reserveSeat(API\RequestedSeat $requestedSeat): bool
    {
        $sector = $requestedSeat->getSector();
        $row = $requestedSeat->getRow();
        $seatInRow = $requestedSeat->getSeatInRow();

        return $this->hallSeats->reserveSeat($sector, $row, $seatInRow);
    }
}
