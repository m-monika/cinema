<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

interface Rule
{
    /**
     * @return bool
     */
    public function canMakeReservation(): bool;
}
