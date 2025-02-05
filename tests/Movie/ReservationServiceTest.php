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
        $this->rulesComposite = $this->createMock(Rules\RuleComposite::class);
        $this->screeningDatabase = $this->createMock(Screening::class);
        $this->reservation = new ReservationService(
            $this->screeningDatabase,
            $this->rulesComposite
        );
    }

    public function testMakeReservationSuccess(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $rule = new Rules\Rule\AndRules();
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getReservation')
            ->willReturn($screeningModel);
        $this->rulesComposite
            ->expects($this->once())
            ->method('getForScreening')
            ->willReturn($rule);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(true);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('saveReservation')
            ->willReturn(true);
        $this->assertInstanceOf(
            Result\Success::class,
            $this->reservation->make(1, new RequestedSeat(1, 1, 1))
        );
    }

    public function testCouldNotSaveToDatabase(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $rule = new Rules\Rule\AndRules();
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getReservation')
            ->willReturn($screeningModel);
        $this->rulesComposite
            ->expects($this->once())
            ->method('getForScreening')
            ->willReturn($rule);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(true);
        $this->screeningDatabase
            ->expects($this->once())
            ->method('saveReservation')
            ->willReturn(false);
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1, 1))
        );
    }

    public function testCouldNotMakeReservation(): void
    {
        $screeningModel = $this->createMock(Model\Reservation::class);
        $rule = new Rules\Rule\AndRules();
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getReservation')
            ->willReturn($screeningModel);
        $this->rulesComposite
            ->expects($this->once())
            ->method('getForScreening')
            ->willReturn($rule);
        $screeningModel
            ->expects($this->once())
            ->method('make')
            ->willReturn(false);
        $this->screeningDatabase
            ->expects($this->never())
            ->method('saveReservation');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1, 1))
        );
    }

    public function testNotFoundMovie(): void
    {
        $this->screeningDatabase
            ->expects($this->once())
            ->method('getReservation')
            ->willReturn(null);
        $this->screeningDatabase
            ->expects($this->never())
            ->method('saveReservation');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1, new RequestedSeat(1, 1, 1))
        );
    }

    public function testNoSeatsChosen(): void
    {
        $this->screeningDatabase
            ->expects($this->never())
            ->method('getReservation');
        $this->assertInstanceOf(
            Result\Failure::class,
            $this->reservation->make(1)
        );
    }
}
