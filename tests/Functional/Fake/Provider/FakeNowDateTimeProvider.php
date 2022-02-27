<?php

declare(strict_types=1);

namespace tests\Meals\Functional\Fake\Provider;

use DateTimeInterface;
use Meals\Application\Component\Provider\NowDateTimeProviderInterface;

class FakeNowDateTimeProvider implements NowDateTimeProviderInterface
{
    private DateTimeInterface $dateTime;

    public function getNowDate(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setNowDate(DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }
}
