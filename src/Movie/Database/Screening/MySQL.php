<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database\Screening;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Database\Screening;
use Cinema\Movie\Model;

class MySQL implements Screening
{
    public function getById(
        int $idScreening,
        RequestedSeat ...$requestedSeats
    ): ?Model\Reservation {
        return null;
    }

    public function save(Model\Reservation $reservation): bool
    {
        return true;
    }
}
