<?php

declare(strict_types=1);

namespace tests\Meals\Unit\Application\Component\Validator;

use Meals\Application\Component\Validator\Exception\PollDoesNotContainDishException;
use Meals\Application\Component\Validator\PollContainsDishValidator;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Dish\DishList;
use Meals\Domain\Menu\Menu;
use Meals\Domain\Poll\Poll;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PollContainsDishValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful(): void
    {
        $poll = $this->prophesize(Poll::class);
        $menu = $this->prophesize(Menu::class);
        $dishList = $this->prophesize(DishList::class);
        $dish = $this->prophesize(Dish::class);

        $dishList->hasDish($dish)->willReturn(true);
        $menu->getDishes()->willReturn($dishList);
        $poll->getMenu()->willReturn($menu);

        $validator = new PollContainsDishValidator();
        verify($validator->validate($poll->reveal(), $dish->reveal()))->null();
    }

    public function testFail(): void
    {
        $this->expectException(PollDoesNotContainDishException::class);

        $poll = $this->prophesize(Poll::class);
        $menu = $this->prophesize(Menu::class);
        $dishList = $this->prophesize(DishList::class);
        $dish = $this->prophesize(Dish::class);

        $dishList->hasDish($dish)->willReturn(false);
        $menu->getDishes()->willReturn($dishList);
        $poll->getMenu()->willReturn($menu);

        $validator = new PollContainsDishValidator();
        $validator->validate($poll->reveal(), $dish->reveal());
    }
}
