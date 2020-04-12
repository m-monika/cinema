<?php
declare(strict_types = 1);

namespace Cinema\Movie;

class Reservation
{
    /**
     * @var Rules\Database
     */
    private $rulesDatabase;

    /**
     * @var Database\Screening
     */
    private $screeningDatabase;

    /**
     * @param Database\Screening $screeningDatabase
     * @param Rules\Database $rulesDatabase
     */
    public function __construct(
        Database\Screening $screeningDatabase,
        Rules\Database $rulesDatabase
    ) {
        $this->rulesDatabase = $rulesDatabase;
        $this->screeningDatabase = $screeningDatabase;
    }

    /**
     * @param int $idScreening
     * @param API\RequestedSeat ...$seats
     *
     * @return Result
     */
    public function make(int $idScreening, API\RequestedSeat ...$seats): Result
    {
        if (count($seats) === 0) {
            return new Result\Failure(
                "You need to choose seats to make a reservation."
            );
        }

        $screening = $this->screeningDatabase->getById($idScreening);

        if ($screening === null) {
            return new Result\Failure(
                "Screening you are looking for does not exits."
            );
        }

        $rule = $this->rulesDatabase->getForMovie($idScreening);
        $result = $screening->makeReservation($rule, ...$seats);

        if ($result === false) {
            return new Result\Failure(
                "You can not reserve those seats."
            );
        }

        if ($this->screeningDatabase->save($screening)) {
            return new Result\Success();
        }

        return new Result\Failure("Something went wrong, try again later.");
    }
}
