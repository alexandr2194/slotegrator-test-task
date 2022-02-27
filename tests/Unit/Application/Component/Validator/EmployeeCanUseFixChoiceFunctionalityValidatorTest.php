<?php

namespace tests\Meals\Unit\Application\Component\Validator;

use DateTime;
use DateTimeInterface;
use Meals\Application\Component\Provider\NowDateTimeProviderInterface;
use Meals\Application\Component\Validator\EmployeeCanUseFixChoiceFunctionalityValidator;
use Meals\Application\Component\Validator\Exception\WrongDateForFixChoiceInPollException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class EmployeeCanUseFixChoiceFunctionalityValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful(): void
    {
        $validator = $this->prepareValidatorCase(new DateTime('2022-02-21 10:00:00'));
        verify($validator->validate())->null();
    }

    public function testWrongDate(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);

        $validator = $this->prepareValidatorCase(new DateTime('2022-02-20 10:00:00'));
        $validator->validate();
    }

    public function testDateWithWrongTimeBeforeSixAM(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);

        $validator = $this->prepareValidatorCase(new DateTime('2022-02-21 05:59:59'));
        $validator->validate();
    }

    public function testDateWithWrongTimeAfterTenPM(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);

        $validator = $this->prepareValidatorCase(new DateTime('2022-02-21 22:00:00'));
        $validator->validate();
    }

    private function prepareValidatorCase(DateTimeInterface $nowDateTime): EmployeeCanUseFixChoiceFunctionalityValidator {
        $nowDateTimeProvider = $this->prophesize(NowDateTimeProviderInterface::class);
        $nowDateTimeProvider->getNowDate()->willReturn($nowDateTime);

        return new EmployeeCanUseFixChoiceFunctionalityValidator($nowDateTimeProvider->reveal());
    }
}
