<?php

declare(strict_types=1);

namespace Meals\Application\Component\Provider;

use DateTimeInterface;

interface NowDateTimeProviderInterface
{
    public function getNowDate(): DateTimeInterface;
}
