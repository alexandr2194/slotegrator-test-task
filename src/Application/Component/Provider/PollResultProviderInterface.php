<?php

declare(strict_types=1);

namespace Meals\Application\Component\Provider;

use DateTimeInterface;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\PollResult;

interface PollResultProviderInterface
{
    public function fixPollResult(PollResult $pollResult): void;

    public function doesEmployeeAlreadyHavePollResultOnDeterminedDate(Employee $employee, DateTimeInterface $dateTime): bool;

    public function getNextId(): int;
}
