<?php

declare(strict_types=1);

namespace tests\Meals\Functional\Interactor;

use DateTime;
use DateTimeInterface;
use Meals\Application\Component\Validator\Exception\AccessDeniedException;
use Meals\Application\Component\Validator\Exception\EmployeeAlreadyHasFixedChoiceInActivePollOnDateException;
use Meals\Application\Component\Validator\Exception\PollDoesNotContainDishException;
use Meals\Application\Component\Validator\Exception\PollIsNotActiveException;
use Meals\Application\Feature\Poll\UseCase\EmployeeFixesChoiceInActivePoll\Interactor;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Dish\DishList;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Menu\Menu;
use Meals\Domain\Poll\Exception\WrongDateForFixChoiceInPollException;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;
use Meals\Domain\User\Permission\Permission;
use Meals\Domain\User\Permission\PermissionList;
use Meals\Domain\User\User;
use tests\Meals\Functional\Fake\Provider\FakeDishProvider;
use tests\Meals\Functional\Fake\Provider\FakeEmployeeProvider;
use tests\Meals\Functional\Fake\Provider\FakeNowDateTimeProvider;
use tests\Meals\Functional\Fake\Provider\FakePollProvider;
use tests\Meals\Functional\Fake\Provider\FakePollResultProvider;
use tests\Meals\Functional\FunctionalTestCase;

class EmployeeFixesChoiceInActivePollTest extends FunctionalTestCase
{
    public function testSuccessful(): void
    {
        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();
        $pollResult = $this->performTestMethod($employee, $poll, $dish, $date);

        verify($pollResult->getId())->equals(1);
        verify($pollResult->getDish())->equals($dish);
        verify($pollResult->getPoll())->equals($poll);
        verify($pollResult->getEmployee())->equals($employee);
        verify($pollResult->getDateTime())->equals($date);
    }

    public function testPollIsNotActive(): void
    {
        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList(isActivePoll: false);
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();

        $this->expectException(PollIsNotActiveException::class);

        $this->performTestMethod($employee, $poll, $dish, $date);
    }

    public function testUserHasNotPermissions(): void
    {
        $employee = $this->getEmployeeWithNoPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();

        $this->expectException(AccessDeniedException::class);

        $this->performTestMethod($employee, $poll, $dish, $date);
    }

    public function testEmployeeAlreadyHasPollResultsInDay(): void
    {
        $this->expectException(EmployeeAlreadyHasFixedChoiceInActivePollOnDateException::class);

        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();
        $pollResult = $this->getPollResult($poll, $employee, $dish, $date);

        $this->performTestMethod($employee, $poll, $dish, $date, $pollResult);
    }

    public function testEmployeeAlreadyHasPollResultsInAnotherPollInDay(): void
    {
        $this->expectException(EmployeeAlreadyHasFixedChoiceInActivePollOnDateException::class);

        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();
        $anotherPoll = $this->getPollWithNotEmptyDishList(2);
        $pollResult = $this->getPollResult($anotherPoll, $employee, $dish, $date);

        $this->performTestMethod($employee, $poll, $dish, $date, $pollResult);
    }

    public function testEmployeeAlreadyHasPollResultsInAnotherDay(): void
    {
        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();
        $previousDate = $this->getCorrectDateTimeInPreviousPeriod();
        $pollResult = $this->getPollResult($poll, $employee, $dish, $previousDate);

        $actualPollResult = $this->performTestMethod($employee, $poll, $dish, $date, $pollResult);

        verify($actualPollResult->getDish())->equals($dish);
        verify($actualPollResult->getPoll())->equals($poll);
        verify($actualPollResult->getEmployee())->equals($employee);
        verify($actualPollResult->getDateTime())->equals($date);
    }

    public function testWrongDate(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);

        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getWrongDateTime();

        $this->performTestMethod($employee, $poll, $dish, $date);
    }

    public function testWrongTime(): void
    {
        $this->expectException(WrongDateForFixChoiceInPollException::class);

        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithNotEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getDateWithWrongTime();

        $this->performTestMethod($employee, $poll, $dish, $date);
    }

    private function performTestMethod(
        Employee          $employee,
        Poll              $poll,
        Dish              $dish,
        DateTimeInterface $nowDateTime,
        PollResult        $pollResult = null
    ): PollResult {
        $this->getContainer()->get(FakeDishProvider::class)->setDish($dish);
        $this->getContainer()->get(FakeEmployeeProvider::class)->setEmployee($employee);
        $this->getContainer()->get(FakePollProvider::class)->setPoll($poll);
        $this->getContainer()->get(FakeNowDateTimeProvider::class)->setNowDate($nowDateTime);

        if ($pollResult) {
            $this->getContainer()->get(FakePollResultProvider::class)->addPollResult($pollResult);
        }

        return $this->getContainer()->get(Interactor::class)->fixChoiceInPoll(
            $employee->getId(),
            $poll->getId(),
            $dish->getId()
        );
    }

    public function testPollNotContainsDish(): void
    {
        $this->expectException(PollDoesNotContainDishException::class);

        $employee = $this->getEmployeeWithPermissions();
        $poll = $this->getPollWithEmptyDishList();
        $dish = $this->getDish();
        $date = $this->getCorrectDateTime();

        $this->performTestMethod($employee, $poll, $dish, $date);
    }

    private function getEmployeeWithPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithPermissions(),
            4,
            'Surname'
        );
    }

    private function getUserWithPermissions(): User
    {
        return new User(
            1,
            new PermissionList(
                [
                    new Permission(Permission::PARTICIPATION_IN_POLLS),
                ]
            ),
        );
    }

    private function getEmployeeWithNoPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithNoPermissions(),
            4,
            'Surname'
        );
    }

    private function getUserWithNoPermissions(): User
    {
        return new User(
            1,
            new PermissionList([]),
        );
    }

    private function getPollWithNotEmptyDishList(int $id  = 1, bool $isActivePoll = true): Poll
    {
        return new Poll(
            $id,
            $isActivePoll,
            new Menu(
                1,
                'title',
                new DishList([
                    new Dish(
                        1,
                        'dish',
                        'Dish description'
                    )
                ]),
            )
        );
    }

    private function getPollWithEmptyDishList(): Poll
    {
        return new Poll(
            1,
            true,
            new Menu(
                1,
                'title',
                new DishList([]),
            )
        );
    }

    private function getDish(): Dish
    {
        return new Dish(
            1,
            'dish',
            'Dish description'
        );
    }

    private function getCorrectDateTime(): DateTimeInterface
    {
        return new DateTime('2022-02-21 10:00:00');
    }

    private function getCorrectDateTimeInPreviousPeriod(): DateTimeInterface
    {
        return new DateTime('2022-02-14 10:00:00');
    }

    private function getWrongDateTime(): DateTimeInterface
    {
        return new DateTime('2022-02-20 07:00:00');
    }

    private function getDateWithWrongTime(): DateTimeInterface
    {
        return new DateTime('2022-02-21 05:00:00');
    }

    private function getPollResult(Poll $poll, Employee $employee, Dish $dish, DateTimeInterface $dateTime): PollResult
    {
        return new PollResult(
            1,
            $poll,
            $employee,
            $dish,
            $dateTime
        );
    }
}