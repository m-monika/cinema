<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

use Cinema\Movie\API;

interface Database
{
    /**
     * @param int $idMovie
     * @param API\RequestedSeat ...$seats
     *
     * @return Rule|null
     */
    public function getForMovie(int $idMovie, API\RequestedSeat ...$seats): ?Rule;
}
