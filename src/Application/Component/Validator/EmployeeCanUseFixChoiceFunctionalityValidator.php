<?php

declare(strict_types=1);

namespace Meals\Application\Component\Validator;

use DateTimeInterface;
use Meals\Application\Component\Validator\Exception\WrongDateForFixChoiceInPollException;

class EmployeeCanUseFixChoiceFunctionalityValidator
{
    private const AVAILABLE_WEEK_DAY_NUMBER                = 1;
    private const START_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER = 6;
    private const END_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER   = 21;

    public function validate(DateTimeInterface $dateTime): void
    {
        if ((int)$dateTime->format('N') !== self::AVAILABLE_WEEK_DAY_NUMBER) {
            throw new WrongDateForFixChoiceInPollException();
        }

        $hour = (int)$dateTime->format('G');
        if ($hour < self::START_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER || $hour > self::END_OF_AVAILABLE_PERIOD_IN_HOUR_NUMBER) {
            throw new WrongDateForFixChoiceInPollException();
        }
    }
}
