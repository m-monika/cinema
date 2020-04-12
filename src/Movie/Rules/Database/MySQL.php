<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules\Database;

use Cinema\Movie\Rules\Database;
use Cinema\Movie\Rules\Rule;

class MySQL implements Database
{
    public function getForMovie(int $idMovie): ?Rule
    {
        return null;
    }
}
