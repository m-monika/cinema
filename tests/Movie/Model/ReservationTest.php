<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Model;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model\HallSeats;
use Cinema\Movie\Model\Reservation;
use Cinema\Movie\Model\Seat;
use Cinema\Movie\Rules\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @covers Reservation
 */
class ReservationTest extends TestCase
{
    public function testMakeReservationNoRule(): void
    {
        $reservation = new Reservation(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $this->assertTrue(
            $reservation->make(
                null,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationWithRuleCanMakeReservation(): void
    {
        $ruleMock = $this->createMock(Rule::class);

        $reservation = new Reservation(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $ruleMock
            ->expects($this->once())
            ->method('canMakeReservation')
            ->willReturn(true);

        $this->assertTrue(
            $reservation->make(
                $ruleMock,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationWithRuleCanNotMakeReservation(): void
    {
        $ruleMock = $this->createMock(Rule::class);

        $reservation = new Reservation(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $ruleMock
            ->expects($this->once())
            ->method('canMakeReservation')
            ->willReturn(false);

        $this->assertFalse(
            $reservation->make(
                $ruleMock,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationButSeatIsTaken(): void
    {
        $reservation = new Reservation(
            1,
            new HallSeats(
                new Seat(1, 1, false)
            )
        );

        $this->assertFalse(
            $reservation->make(
                null,
                new RequestedSeat(1, 1)
            )
        );
    }
}
