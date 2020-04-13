<?php
declare(strict_types = 1);

namespace Cinema\Movie;

class ReservationService
{
    /**
     * @var Rules\RuleComposite
     */
    private $rulesComposite;

    /**
     * @var Database\Screening
     */
    private $reservationDatabase;

    /**
     * @param Database\Screening $reservationDatabase
     * @param Rules\RuleComposite $rulesComposite
     */
    public function __construct(
        Database\Screening $reservationDatabase,
        Rules\RuleComposite $rulesComposite
    ) {
        $this->rulesComposite = $rulesComposite;
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
        if (!$this->validateRequestedSeats($seats)) {
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

        if (!$this->tryMakeReservation($idScreening, $reservation, $seats)) {
            return new Result\Failure(
                "You can not reserve those seats."
            );
        }

        if ($this->saveReservation($reservation)) {
            return new Result\Success();
        }

        return new Result\Failure("Something went wrong, try again later.");
    }

    private function validateRequestedSeats(array $seats): bool
    {
        return count($seats) > 0;
    }

    private function tryMakeReservation(
        int $idScreening,
        Model\Reservation $reservation,
        array $seats
    ): bool {
        $rule = $this->rulesComposite->getForScreening($idScreening);

        return $reservation->make($rule, ...$seats);
    }

    private function saveReservation(Model\Reservation $reservation): bool
    {
        return $this->reservationDatabase->save($reservation);
    }
}
