<?php
declare(strict_types = 1);

namespace Cinema\Tests\Movie\Rules\Rule;

use Cinema\Movie\API\RequestedSeat;
use Cinema\Movie\Model;
use Cinema\Movie\ReservationService;
use Cinema\Movie\Result;
use Cinema\Movie\Rules;
use Cinema\Movie\Database\Screening;
use PHPUnit\Framework\TestCase;

/**
 * @covers ReservationService
 */
class ReservationServiceTest extends TestCase
{
    public function setUp(): void
    {
        $this->rulesDatabase = $this->createMock(Rules\Database::class);
        $this->screeningDatabase = $this->createMock(Screening::class);
        $this->reservation = new ReservationService(
            $this->screeningDatabase,
            $this->rulesDatabase
        );
    }

    public function testMakeReservationSuccess(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getById')
            ->willReturn($screeningModel);
        $this->rulesDatabase
            ->expects($this->once())
            ->method('getForMovie')
            ->willReturn(null);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(true);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('save')
            ->willReturn(true);
        $this->assertInstanceOf(
            Result\Success::class,
            $this->reservation->make(1, new RequestedSeat(1, 1))
        );
    }

    public function testCouldNotSaveToDatabase(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getById')
            ->willReturn($screeningModel);
        $this->rulesDatabase
            ->expects($this->once())
            ->method('getForMovie')
            ->willReturn(null);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(true);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('save')
            ->willReturn(false);
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1))
        );
    }

    public function testCouldNotMakeReservation(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getById')
            ->willReturn($screeningModel);
        $this->rulesDatabase
            ->expects($this->once())
            ->method('getForMovie')
            ->willReturn(null);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(false);
        $this->screeningDatabase
            ->expects($this->never())
            ->method('save');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1))
        );
    }

    public function testNotFoundMovie(): void
    {
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getById')
            ->willReturn(null);
        $this->screeningDatabase
            ->expects($this->never())
            ->method('save');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1))
        );
    }

    public function testNoSeatsChosen(): void
    {
        $this->screeningDatabase
            ->expects($this->never())
            ->method('getById');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1)
        );
    }
}
