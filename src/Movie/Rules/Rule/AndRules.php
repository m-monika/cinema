<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Rules\Rule;

class AndRules implements Rule
{
    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * @param Rule ...$rules
     */
    public function __construct(Rule ...$rules)
    {
        $this->rules = $rules;
    }

    public function canMakeReservation(RequestedSeat ...$requestedSeats): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->canMakeReservation(...$requestedSeats)) {
                return false;
            }
        }

        return true;
    }
}
