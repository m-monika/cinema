<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model\HallSeats;
use Cinema\Movie\Model\Seat;
use Cinema\Movie\Rules\Rule\LeftSeatsOnTheSides;
use PHPUnit\Framework\TestCase;

/**
 * @covers LeftSeatsOnTheSides
 */
class LeftSeatsOnTheSidesTest extends TestCase
{
    public function seatsProvider(): array
    {
        return [
            'reservation-for-first-seat-in-row' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                2,
                [new RequestedSeat(1, 1, 1)],
                true
            ],
            'reservation-for-second-seat-in-row-when-two-seats-must-be-left' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                2,
                [new RequestedSeat(1, 1, 2)],
                false
            ],
            'reservation-for-second-seat-in-row-when-one-seats-must-be-left' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                1,
                [new RequestedSeat(1, 1, 2)],
                true
            ],
            'reservation-for-second-seat-in-row-when-first-seat-is-taken' => [
                new HallSeats(
                    new Seat(1, 1, 1, false),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                1,
                [new RequestedSeat(1, 1, 2)],
                true
            ],
            'reservation-for-third-seat-in-row-when-two-seats-must-be-left' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                2,
                [new RequestedSeat(1, 1, 3)],
                true
            ],
            'reservation-for-third-seat-in-row-when-two-seats-must-be-left-first-seat-is-taken' => [
                new HallSeats(
                    new Seat(1, 1, 1, false),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true)
                ),
                2,
                [new RequestedSeat(1, 1, 3)],
                false
            ],
            'reservation-for-third-seat-in-row-when-second-seat-is-taken' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, false),
                    new Seat(1, 1, 3, true)
                ),
                2,
                [new RequestedSeat(1, 1, 3)],
                true
            ],
            'reservation-for-few-rows' => [
                new HallSeats(
                    new Seat(1, 1, 1, true),
                    new Seat(1, 1, 2, true),
                    new Seat(1, 1, 3, true),
                    new Seat(1, 1, 4, true),
                    new Seat(1, 2, 1, true),
                    new Seat(1, 2, 2, true),
                    new Seat(1, 2, 3, true),
                    new Seat(1, 2, 4, true),
                ),
                2,
                [
                    new RequestedSeat(1, 1, 1),
                    new RequestedSeat(1, 1, 2),
                    new RequestedSeat(1, 2, 1),
                    new RequestedSeat(1, 2, 2),
                ],
                false
            ]
        ];
    }

    /**
     * @dataProvider seatsProvider
     */
    public function testCheckSides(
        HallSeats $hallSeats,
        int $seatOnSideToLeave,
        array $requestedSeats,
        bool $result
    ): void {
        $leftSeatsOnTheSides = new LeftSeatsOnTheSides(
            $seatOnSideToLeave,
            $hallSeats
        );
        $this->assertSame(
            $result,
            $leftSeatsOnTheSides->canMakeReservation(...$requestedSeats)
        );
    }
}
