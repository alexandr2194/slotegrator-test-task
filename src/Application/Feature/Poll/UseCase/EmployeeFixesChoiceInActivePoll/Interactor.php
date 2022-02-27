<?php

declare(strict_types=1);

namespace Meals\Application\Feature\Poll\UseCase\EmployeeFixesChoiceInActivePoll;

use DateTimeInterface;
use Meals\Application\Component\Provider\DishProviderInterface;
use Meals\Application\Component\Provider\EmployeeProviderInterface;
use Meals\Application\Component\Provider\NowDateTimeProviderInterface;
use Meals\Application\Component\Provider\PollProviderInterface;
use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Application\Component\Validator\PollContainsDishValidator;
use Meals\Application\Component\Validator\PollIsActiveValidator;
use Meals\Application\Component\Validator\UserHasAccessToParticipationInPollsValidator;
use Meals\Application\Component\Validator\EmployeeHasNotFixedChoiceYetValidatorOnDate;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;

class Interactor
{
    public function __construct(
        private EmployeeProviderInterface                    $employeeProvider,
        private PollProviderInterface                        $pollProvider,
        private DishProviderInterface                        $dishProvider,
        private PollResultProviderInterface                  $pollResultProvider,
        private NowDateTimeProviderInterface                 $nowDateTimeProvider,
        private UserHasAccessToParticipationInPollsValidator $userHasAccessToParticipationInPollsValidator,
        private PollIsActiveValidator                        $pollIsActiveValidator,
        private PollContainsDishValidator                    $pollContainsDishValidator,
        private EmployeeHasNotFixedChoiceYetValidatorOnDate  $userHasNotFixedChoiceYetValidator,
    ) {
    }

    public function fixChoiceInPoll(int $employeeId, int $pollId, int $dishId): PollResult
    {
        $poll = $this->pollProvider->getPoll($pollId);
        $this->pollIsActiveValidator->validate($poll);

        $employee = $this->employeeProvider->getEmployee($employeeId);
        $this->userHasAccessToParticipationInPollsValidator->validate($employee->getUser());

        $now = $this->nowDateTimeProvider->getNowDate();
        $this->userHasNotFixedChoiceYetValidator->validate($employee, $now);

        $dish = $this->dishProvider->getDish($dishId);
        $this->pollContainsDishValidator->validate($poll, $dish);

        $pollResult = $this->createPollResult($poll, $employee, $dish, $now);
        $this->pollResultProvider->fixPollResult($pollResult);

        return $pollResult;
    }

    public function createPollResult(Poll $poll, Employee $employee, Dish $dish, DateTimeInterface $now): PollResult
    {
        return new PollResult(
            $this->pollResultProvider->getNextId(),
            $poll,
            $employee,
            $dish,
            $now
        );
    }
}
