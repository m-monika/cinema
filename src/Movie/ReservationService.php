<?php
declare(strict_types = 1);

namespace Cinema\Movie;

class ReservationService
{
    /**
     * @var Rules\Database
     */
    private $rulesDatabase;

    /**
     * @var Database\Screening
     */
    private $reservationDatabase;

    /**
     * @param Database\Screening $reservationDatabase
     * @param Rules\Database $rulesDatabase
     */
    public function __construct(
        Database\Screening $reservationDatabase,
        Rules\Database $rulesDatabase
    ) {
        $this->rulesDatabase = $rulesDatabase;
        $this->reservationDatabase = $reservationDatabase;
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

        $reservation = $this->reservationDatabase->getById($idScreening);

        if ($reservation === null) {
            return new Result\Failure(
                "Screening you are looking for does not exits."
            );
        }

        $rule = $this->rulesDatabase->getForMovie($idScreening);
        $result = $reservation->make($rule, ...$seats);

        if ($result === false) {
            return new Result\Failure(
                "You can not reserve those seats."
            );
        }

        if ($this->reservationDatabase->save($reservation)) {
            return new Result\Success();
        }

        return new Result\Failure("Something went wrong, try again later.");
    }
}
