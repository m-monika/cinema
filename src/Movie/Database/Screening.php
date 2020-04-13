<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model;

interface Screening
{
    /**
     * @param int $idScreening
     * @param RequestedSeat ...$seats
     *
     * @return Model\Reservation|null
     */
    public function getById(
        int $idScreening,
        RequestedSeat ...$seats
    ): ?Model\Reservation;

    /**
     * @param Model\Reservation $reservation
     *
     * @return bool
     */
    public function save(Model\Reservation $reservation): bool;
}
