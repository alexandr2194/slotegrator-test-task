<?php

declare(strict_types=1);

namespace Meals\Domain\Poll;

use DateTimeInterface;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Exception\WrongDateForFixChoiceInPollException;

class PollResult
{
    private const AVAILABLE_WEEK_DAY_NUMBER                = 1;
    private const START_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER = 6;
    private const END_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER   = 21;

    public function __construct(
        private int               $id,
        private Poll              $poll,
        private Employee          $employee,
        private Dish              $dish,
        private DateTimeInterface $dateTime
    ) {
        $this->assertDateTimeIsValid($this->dateTime);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPoll(): Poll
    {
        return $this->poll;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getDish(): Dish
    {
        return $this->dish;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    private function assertDateTimeIsValid(DateTimeInterface $dateTime): void
    {
        if ((int)$dateTime->format('N') !== self::AVAILABLE_WEEK_DAY_NUMBER) {
            throw new WrongDateForFixChoiceInPollException();
        }

        $hour = (int)$dateTime->format('G');
        if ($hour < self::START_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER || $hour > self::END_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER) {
            throw new WrongDateForFixChoiceInPollException();
        }
    }
}
