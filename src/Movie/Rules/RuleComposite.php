<?php
declare(strict_types = 1);

namespace Cinema\Movie\Rules;

class RuleComposite
{
    /**
     * @var Database
     */
    private $rulesDatabase;

    /**
     * @param Database $rulesDatabase
     */
    public function __construct(Database $rulesDatabase)
    {
        $this->rulesDatabase = $rulesDatabase;
    }

    /**
     * @param int $idScreening
     *
     * @return Rule
     */
    public function getForScreening(int $idScreening): Rule
    {
        $allRules = [];
        $ruleForMovie = $this->rulesDatabase->getForMovie($idScreening);
        if ($ruleForMovie !== null) {
            $allRules[] = $ruleForMovie;
        }

        return new Rule\AndRules(...$allRules);
    }
}
