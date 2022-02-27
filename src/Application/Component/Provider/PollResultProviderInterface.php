<?php

declare(strict_types=1);

namespace Meals\Application\Component\Provider;

use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;

interface PollResultProviderInterface
{
    public function fixPollResult(PollResult $pollResult): void;

    public function doesEmployeeAlreadyHavePollResult(Employee $employee, Poll $poll): bool;

    public function getNextId(): int;
}
