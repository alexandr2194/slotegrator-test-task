<?php

declare(strict_types=1);

namespace tests\Meals\Unit\Application\Component\Validator;

use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Application\Component\Validator\Exception\EmployeeAlreadyHasFixedChoiceInPollException;
use Meals\Application\Component\Validator\EmployeeHasNotFixedChoiceInPollYetValidator;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Poll;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class EmployeeHasNotFixedChoiceInPollYetValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful(): void
    {
        $employee = $this->prophesize(Employee::class);
        $poll = $this->prophesize(Poll::class);

        $pollResultProvider = $this->prophesize(PollResultProviderInterface::class);
        $pollResultProvider->doesEmployeeAlreadyHavePollResult($employee, $poll)->willReturn(false);

        $validator = new EmployeeHasNotFixedChoiceInPollYetValidator($pollResultProvider->reveal());
        verify($validator->validate($employee->reveal(), $poll->reveal()))->null();
    }

    public function testFail(): void
    {
        $this->expectException(EmployeeAlreadyHasFixedChoiceInPollException::class);

        $employee = $this->prophesize(Employee::class);
        $poll = $this->prophesize(Poll::class);

        $pollResultProvider = $this->prophesize(PollResultProviderInterface::class);
        $pollResultProvider->doesEmployeeAlreadyHavePollResult($employee, $poll)->willReturn(true);

        $validator = new EmployeeHasNotFixedChoiceInPollYetValidator($pollResultProvider->reveal());
        verify($validator->validate($employee->reveal(), $poll->reveal()))->null();
    }
}
