<?php
declare(strict_types = 1);

namespace Cinema\Movie\Database\Screening;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Database\Screening;
use Cinema\Movie\Model;

class MySQL implements Screening
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllHallSeats(int $idScreening): ?Model\HallSeats
    {
        $statement = $this->pdo->prepare($this->getBaseSqlForSeats());
        $statement->bindParam(':id_screening', $idScreening, \PDO::PARAM_INT);
        $statement->execute();
        $seats = $this->fetchSeats($statement);

        if (count($seats) === 0) {
            return null;
        }

        return new Model\HallSeats(...$seats);
    }

    public function getReservation(
        int $idScreening,
        RequestedSeat ...$requestedSeats
    ): ?Model\Reservation {
        $statement = $this->getStatementForFiningSeats(
            $idScreening,
            $requestedSeats
        );
        $seats = $this->fetchSeats($statement);

        if (count($seats) !== count($requestedSeats)) {
            return null;
        }

        return new Model\Reservation(
            $idScreening,
            new Model\HallSeats(...$seats)
        );
    }

    private function getStatementForFiningSeats(
        int $idScreening,
        array $requestedSeats
    ): \PDOStatement {
        $sql = $this->getBaseSqlForSeats()
            . ' AND ' . $this->getSqlForRequestedSeats($requestedSeats);
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id_screening', $idScreening, \PDO::PARAM_INT);
        $statement->execute();

        return $statement;
    }

    private function getBaseSqlForSeats(): string
    {
        return <<<SQL
            SELECT
                `screening_seats`.`sector`,
                `screening_seats`.`row`,
                `screening_seats`.`seat_in_row`,
                `screening_seats`.`available`,
                `screening_seats`.`version`
            FROM `screening_seats`
            LEFT JOIN `screening`
                ON `screening_seats`.`id_screening`=`screening`.`id_screening`
            WHERE `screening_seats`.`id_screening` = :id_screening
        SQL;
    }

    private function getSqlForRequestedSeats(array $requestedSeats): string
    {
        $requestedSeatsSql = [];

        foreach ($requestedSeats as $requestedSeat) {
            $requestedSeatsSql[] = <<<SQL
                (
                    `screening_seats`.`sector` = {$requestedSeat->getSector()}
                    AND `screening_seats`.`row` = {$requestedSeat->getRow()}
                    AND `screening_seats`.`seat_in_row` = {$requestedSeat->getSeatInRow()}
                )
            SQL;
        }

        return '('. implode(" OR ", $requestedSeatsSql) . ')';
    }

    private function fetchSeats(\PDOStatement $statement): array
    {
        $seats = [];

        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $seat) {
            $seats[] = new Model\Seat(
                (int) $seat['sector'],
                (int) $seat['row'],
                (int) $seat['seat_in_row'],
                $seat['available'] === 'true',
                (int) $seat['version']
            );
        }

        return $seats;
    }

    public function saveReservation(Model\Reservation $reservation): bool
    {
        $seats = $this->getSeats($reservation);
        $idScreening = $this->getIdScreening($reservation);
        $sql = $this->getUpdateSqlForSeats($idScreening, $seats);
        $this->pdo->beginTransaction();
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id_screening', $idScreening, \PDO::PARAM_INT);

        if (!$statement->execute() || $statement->rowCount() !== count($seats)) {
            $this->pdo->rollback();

            return false;
        }

        $this->pdo->commit();

        return true;
    }

    private function getSeats(Model\Reservation $reservation): array
    {
        $reflectionReservation = new \ReflectionClass($reservation);
        $hallSeatsProperty = $reflectionReservation->getProperty('hallSeats');
        $hallSeatsProperty->setAccessible(true);
        $hallSeats = $hallSeatsProperty->getValue($reservation);
        $reflectionHallSeats = new \ReflectionClass($hallSeats);
        $seatsProperty = $reflectionHallSeats->getProperty('seats');
        $seatsProperty->setAccessible(true);
        $seatsArray = $seatsProperty->getValue($hallSeats);
        $seats = [];

        foreach ($seatsArray as $section) {
            foreach ($section as $rows) {
                foreach ($rows as $seat) {
                    $seats[] = $seat;
                }
            }
        }

        return $seats;
    }

    private function getIdScreening(Model\Reservation $reservation): int
    {
        $reflectionReservation = new \ReflectionClass($reservation);
        $idScreeningProperty = $reflectionReservation->getProperty('idScreening');
        $idScreeningProperty->setAccessible(true);

        return $idScreeningProperty->getValue($reservation);
    }

    private function getUpdateSqlForSeats(int $idScreening, array $seats): string
    {
        $sqlSeats = [];

        foreach ($seats as $seat) {
            $sqlSeats[] = <<<SQL
                (
                    `dbm`.`screening_seats`.`sector` = {$seat->getSector()}
                    AND `dbm`.`screening_seats`.`row` = {$seat->getRow()}
                    AND `dbm`.`screening_seats`.`seat_in_row` = {$seat->getSeatInRow()}
                )
            SQL;
        }

        $sql = '('. implode(" OR ", $sqlSeats) . ')';

        return <<<SQL
            UPDATE `dbm`.`screening_seats`
            SET `dbm`.`screening_seats`.`available`='false',
                `dbm`.`screening_seats`.`version`=1
            WHERE
                `dbm`.`screening_seats`.`id_screening` = :id_screening
                AND `dbm`.`screening_seats`.`version` = 0
                AND {$sql}
        SQL;
    }
}
