<?php

declare(strict_types=1);

namespace Meals\Application\Component\Validator;

use DateTimeInterface;
use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Application\Component\Validator\Exception\EmployeeAlreadyHasFixedChoiceInActivePollOnDateException;
use Meals\Domain\Employee\Employee;

class EmployeeHasNotFixedChoiceYetValidatorOnDate
{
    public function __construct(
        private PollResultProviderInterface $pollResultProvider
    ) {
    }

    public function validate(Employee $employee, DateTimeInterface $pollResultCreatingDateTime): void
    {
        if ($this->pollResultProvider->doesEmployeeAlreadyHavePollResultOnDeterminedDate($employee, $pollResultCreatingDateTime)) {
            throw new EmployeeAlreadyHasFixedChoiceInActivePollOnDateException();
        }
    }
}
