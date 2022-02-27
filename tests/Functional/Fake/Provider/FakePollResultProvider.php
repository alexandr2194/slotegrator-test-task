<?php

declare(strict_types=1);

namespace tests\Meals\Functional\Fake\Provider;

use DateTimeInterface;
use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\PollResult;

class FakePollResultProvider implements PollResultProviderInterface
{
    /**
     * @var PollResult[]
     */
    private array $pollResults = [];

    public function fixPollResult(PollResult $pollResult): void
    {
        $this->pollResults[] = $pollResult;
    }

    public function addPollResult(PollResult $pollResult): void
    {
        $this->pollResults[] = $pollResult;
    }

    /**
     * @return PollResult[]
     */
    public function getPollResults(): array
    {
        return $this->pollResults;
    }

    public function doesEmployeeAlreadyHavePollResultOnDeterminedDate(Employee $employee, DateTimeInterface $dateTime): bool
    {
        foreach ($this->pollResults as $pollResult) {
            if ($pollResult->getEmployee() !== $employee) {
                continue;
            }

            if ($pollResult->getDateTime()->format('Y-m-d') === $dateTime->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    public function getNextId(): int
    {
        return 1;
    }
}
