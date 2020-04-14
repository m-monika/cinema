<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database\Screening;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Database\Screening;
use Cinema\Movie\Model;

class MySQL implements Screening
{
    public function getReservation(
        int $idScreening,
        RequestedSeat ...$requestedSeats
    ): ?Model\Reservation {
        return null;
    }

    public function saveReservation(Model\Reservation $reservation): bool
    {
        return true;
    }

    public function getAllHallSeats(int $idScreening): ?Model\HallSeats
    {
        return new Model\HallSeats(
            new Model\Seat(1, 1, 1, true, 0),
            new Model\Seat(1, 1, 2, true, 0),
            new Model\Seat(1, 1, 3, true, 0),
            new Model\Seat(1, 1, 4, true, 0)
        );
    }
}
