<?php

declare(strict_types=1);

namespace tests\Meals\Unit\Application\Component\Validator;

use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Application\Component\Validator\Exception\EmployeeAlreadyHasFixedChoiceInActivePollOnDateException;
use Meals\Application\Component\Validator\EmployeeHasNotFixedChoiceYetValidatorOnDate;
use Meals\Domain\Employee\Employee;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserHasNotFixedChoiceYetValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful(): void
    {
        $employee = $this->prophesize(Employee::class);

        $date = new \DateTime();

        $pollResultProvider = $this->prophesize(PollResultProviderInterface::class);
        $pollResultProvider->doesEmployeeAlreadyHavePollResultOnDeterminedDate($employee, $date)->willReturn(false);

        $validator = new EmployeeHasNotFixedChoiceYetValidatorOnDate($pollResultProvider->reveal());
        verify($validator->validate($employee->reveal(), $date))->null();
    }

    public function testFail(): void
    {
        $this->expectException(EmployeeAlreadyHasFixedChoiceInActivePollOnDateException::class);

        $employee = $this->prophesize(Employee::class);

        $date = new \DateTime();

        $pollResultProvider = $this->prophesize(PollResultProviderInterface::class);
        $pollResultProvider->doesEmployeeAlreadyHavePollResultOnDeterminedDate($employee, $date)->willReturn(true);

        $validator = new EmployeeHasNotFixedChoiceYetValidatorOnDate($pollResultProvider->reveal());
        verify($validator->validate($employee->reveal(), $date))->null();
    }
}
