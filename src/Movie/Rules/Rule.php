<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

use Cinema\Movie\API\RequestedSeat;

interface Rule
{
    /**
     * @param RequestedSeat ...$requestedSeats
     *
     * @return bool
     */
    public function canMakeReservation(RequestedSeat ...$requestedSeats): bool;
}
