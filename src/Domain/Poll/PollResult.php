<?php

declare(strict_types=1);

namespace Meals\Domain\Poll;

use DateTimeInterface;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Employee\Employee;

class PollResult
{
    public function __construct(
        private int               $id,
        private Poll              $poll,
        private Employee          $employee,
        private Dish              $dish,
        private DateTimeInterface $dateTime
    ) {
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
}
