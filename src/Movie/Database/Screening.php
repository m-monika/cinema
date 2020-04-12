<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database;

use Cinema\Movie\Model;

interface Screening
{
    /**
     * @param int $idScreening
     *
     * @return Model\Reservation|null
     */
    public function getById(int $idScreening): ?Model\Reservation;

    /**
     * @param Model\Reservation $reservation
     *
     * @return bool
     */
    public function save(Model\Reservation $reservation): bool;
}
