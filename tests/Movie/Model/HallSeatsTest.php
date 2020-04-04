<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Model;

use Cinema\Movie\Model\HallSeats;
use Cinema\Movie\Model\Seat;
use PHPUnit\Framework\TestCase;

/**
 * @covers HallSeats
 */
class HallSeatsTest extends TestCase
{
    public function testTakenSeat(): void
    {
        $hallSeat = new HallSeats(new Seat(1, 1, false));
        $this->assertFalse($hallSeat->isSeatAvailable(1, 1));
    }

    public function testNotTakenSeat(): void
    {
        $hallSeat = new HallSeats(new Seat(1, 1, true));
        $this->assertTrue($hallSeat->isSeatAvailable(1, 1));
    }

    public function testSeatDoesntExists(): void
    {
        $hallSeat = new HallSeats(new Seat(1, 1, false));
        $this->expectException(\InvalidArgumentException::class);
        $hallSeat->isSeatAvailable(1, 2);
    }

    public function testMaxSeatsInRow(): void
    {
        $hallSeat = new HallSeats(new Seat(1, 1, false));
        $this->assertSame(1, $hallSeat->maxSeatsInRow(1));
    }

    public function testRowDoesntExists(): void
    {
        $hallSeat = new HallSeats(new Seat(1, 1, false));
        $this->expectException(\InvalidArgumentException::class);
        $hallSeat->maxSeatsInRow(2);
    }
}
