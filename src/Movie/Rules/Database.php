<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

interface Database
{
    /**
     * @param int $idMovie
     *
     * @return Rule|null
     */
    public function getForMovie(int $idMovie): ?Rule;
}
