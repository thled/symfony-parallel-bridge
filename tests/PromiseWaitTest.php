<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use Publicplan\ParallelBridge\Factory\PoolFactory;
use Publicplan\ParallelBridge\PromiseWait;

final class PromiseWaitTest extends TestCase
{
    /** @test */
    public function closure(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $closure = TestClass::getClosure();

        $arg1 = 1;
        $arg2 = 1;

        $result = $subject->parallelMap($array, $closure, $arg1, $arg2);

        $expectedResult = [3, 4, 5, 6, 7, 8, 9, 10, 11];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function staticMethod(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $arg1 = 1;
        $arg2 = 1;

        $result = $subject->parallelMap($array, [TestClass::class, 'addOne'], $arg1, $arg2);

        $expectedResult = [3, 4, 5, 6, 7, 8, 9, 10, 11];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function classSimpleString(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $result = $subject->parallelMap($array, 'is_string');

        $expectedResult = [false, false, false, false, false, false, false, false, false];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function staticMethodAsString(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $arg1 = 1;
        $arg2 = 1;

        $result = $subject->parallelMap($array, TestClass::class . '::addOne', $arg1, $arg2);

        $expectedResult = [3, 4, 5, 6, 7, 8, 9, 10, 11];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function relativeStaticMethod(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $arg1 = 1;

        $arg2 = 1;

        /** @var callable $callable */
        $callable = [TestClass::class, 'parent::getNumber'];
        $result = $subject->parallelMap($array, $callable, $arg1, $arg2);

        $expectedResult = [7, 8, 9, 10, 11, 12, 13, 14, 15];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function invokedMethod(): void
    {
        $poolFactory = new PoolFactory(__DIR__);
        $subject = new PromiseWait($poolFactory, 3);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $testClass = new TestClass();

        $arg1 = 1;

        $arg2 = 2;

        $result = $subject->parallelMap($array, $testClass, $arg1, $arg2);

        $expectedResult = [6, 7, 8, 9, 10, 11, 12, 13, 14];
        self::assertSame($result, $expectedResult);
    }
}
