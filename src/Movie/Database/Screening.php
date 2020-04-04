<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database;

use Cinema\Movie\Model;

interface Screening
{
    /**
     * @param int $idScreening
     *
     * @return Model\Screening|null
     */
    public function getById(int $idScreening): ?Model\Screening;

    /**
     * @param Model\Screening $screening
     *
     * @return bool
     */
    public function save(Model\Screening $screening): bool;
}
