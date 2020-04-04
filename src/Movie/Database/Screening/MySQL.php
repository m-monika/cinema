<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database\Screening;

use Cinema\Movie\Database\Screening;
use Cinema\Movie\Model;

class MySQL implements Screening
{
    public function getById(int $idScreening): ?Model\Screening
    {
        return null;
    }

    public function save(Model\Screening $screening): bool
    {
        return true;
    }
}
