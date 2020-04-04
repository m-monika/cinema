<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Model;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model\HallSeats;
use Cinema\Movie\Model\Screening;
use Cinema\Movie\Model\Seat;
use Cinema\Movie\Rules\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @covers Screening
 */
class ScreeningTest extends TestCase
{
    public function testMakeReservationNoRule(): void
    {
        $screening = new Screening(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $this->assertTrue(
            $screening->makeReservation(
                null,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationWithRuleCanMakeReservation(): void
    {
        $ruleMock = $this->createMock(Rule::class);

        $screening = new Screening(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $ruleMock
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $ruleMock
            ->expects($this->once())
            ->method('canMakeReservation')
            ->willReturn(true);

        $this->assertTrue(
            $screening->makeReservation(
                $ruleMock,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationWithRuleCanNotMakeReservation(): void
    {
        $ruleMock = $this->createMock(Rule::class);

        $screening = new Screening(
            1,
            new HallSeats(
                new Seat(1, 1, true)
            )
        );

        $ruleMock
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $ruleMock
            ->expects($this->once())
            ->method('canMakeReservation')
            ->willReturn(false);

        $this->assertFalse(
            $screening->makeReservation(
                $ruleMock,
                new RequestedSeat(1, 1)
            )
        );
    }

    public function testMakeReservationButSeatIsTaken(): void
    {
        $screening = new Screening(
            1,
            new HallSeats(
                new Seat(1, 1, false)
            )
        );

        $this->assertFalse(
            $screening->makeReservation(
                null,
                new RequestedSeat(1, 1)
            )
        );
    }
}
