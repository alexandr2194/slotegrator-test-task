<?php

declare(strict_types=1);

namespace Meals\Application\Component\Provider;

use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollList;

interface PollProviderInterface
{
    public function getActivePolls(): PollList;

    public function getPoll(int $pollId): Poll;
}
