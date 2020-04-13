<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Rules\Rule\SpaceBetweenRequestedSeats;
use PHPUnit\Framework\TestCase;

/**
 * @covers SpaceBetweenRequestedSeats
 */
class SpaceBetweenRequestedSeatsTest extends TestCase
{
    public function requestedSeatsProvider(): array
    {
        return [
            'only-one-seat-requested' => [
                [new RequestedSeat(1, 1, 1)],
                1,
                true
            ],
            'two-seats-requested-alongside' => [
                [new RequestedSeat(1, 1, 1), new RequestedSeat(1, 1, 2)],
                1,
                true
            ],
            'two-seats-requested-with-only-one-space-seat' => [
                [new RequestedSeat(1, 1, 1), new RequestedSeat(1, 1, 3)],
                1,
                true
            ],
            'two-seats-requested-with-only-one-space-seat-but-two-required' => [
                [new RequestedSeat(1, 1, 1), new RequestedSeat(1, 1, 3)],
                2,
                false
            ],
            'two-seats-requested-in-different-row' => [
                [new RequestedSeat(1, 1, 1), new RequestedSeat(1, 2, 3)],
                2,
                true
            ],
            'four-seats-requested-in-different-row' => [
                [
                    new RequestedSeat(1, 1, 1),
                    new RequestedSeat(1, 1, 2),
                    new RequestedSeat(1, 2, 1),
                    new RequestedSeat(1, 2, 2)
                ],
                2,
                true
            ],
            'four-seats-requested-in-different-row-but-no-space-in-second-row' => [
                [
                    new RequestedSeat(1, 1, 1),
                    new RequestedSeat(1, 1, 2),
                    new RequestedSeat(1, 2, 1),
                    new RequestedSeat(1, 2, 3)
                ],
                2,
                false
            ]
        ];
    }

    /**
     * @dataProvider requestedSeatsProvider
     */
    public function testCanMakeReservation(
        array $requestedSeats,
        int $spaceBetweenSeats,
        bool $result
    ): void {
        $rule = new SpaceBetweenRequestedSeats($spaceBetweenSeats);
        $this->assertSame(
            $result,
            $rule->canMakeReservation(...$requestedSeats)
        );
    }
}
