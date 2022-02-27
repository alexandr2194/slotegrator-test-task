<?php

namespace tests\Meals\Unit\Application\Component\Validator;

use DateTime;
use Meals\Application\Component\Validator\EmployeeCanUseFixChoiceFunctionalityValidator;
use Meals\Application\Component\Validator\Exception\WrongDateForFixChoiceInPollException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class EmployeeCanUseFixChoiceFunctionalityValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful(): void
    {
        $dateTime = new DateTime('2022-02-21 10:00:00');

        $validator = new EmployeeCanUseFixChoiceFunctionalityValidator();
        verify($validator->validate($dateTime))->null();
    }

    public function testWrongDate(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);
        $dateTime = new DateTime('2022-02-20 10:00:00');

        $validator = new EmployeeCanUseFixChoiceFunctionalityValidator();
        $validator->validate($dateTime);
    }

    public function testDateWithWrongTimeBeforeSixAM(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);
        $dateTime = new DateTime('2022-02-21 05:59:59');

        $validator = new EmployeeCanUseFixChoiceFunctionalityValidator();
        $validator->validate($dateTime);
    }

    public function testDateWithWrongTimeAfterTenPM(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);
        $dateTime = new DateTime('2022-02-21 22:00:00');

        $validator = new EmployeeCanUseFixChoiceFunctionalityValidator();
        $validator->validate($dateTime);
    }
}
