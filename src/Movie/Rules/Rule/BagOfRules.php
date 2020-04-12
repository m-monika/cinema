<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules\Rule;

use Cinema\Movie\Rules\Rule;

class BagOfRules implements Rule
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

    public function canMakeReservation(): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->canMakeReservation()) {
                return false;
            }
        }

        return true;
    }
}
