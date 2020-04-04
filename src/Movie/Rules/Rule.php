<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

interface Rule
{
    public function canUse(): bool;

    public function canMakeReservation(): bool;
}
