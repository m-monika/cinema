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

    public function canUse(): bool
    {
        return true;
    }

    public function canMakeReservation(): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->canUse() && !$rule->canMakeReservation()) {
                return false;
            }
        }

        return true;
    }
}
