<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use PP\ParallelBridge\Factory\PoolFactory;
use PP\ParallelBridge\Factory\TestPoolFactory;
use PP\ParallelBridge\PromiseWait;

final class PromiseWaitTest extends TestCase
{
    /** @test */
    public function closure(): void
    {
        $poolFactory = new PoolFactory(3, __DIR__);
        $subject = new PromiseWait($poolFactory);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $closure = TestClosure::getClosure();

        $result = $subject->parallelMap($array, $closure);

        $expectedResult = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function staticMethod(): void
    {
        $poolFactory = new PoolFactory(3, __DIR__);
        $subject = new PromiseWait($poolFactory);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $result = $subject->parallelMap($array, [TestClosure::class,'addOne']);

        $expectedResult = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function classSimpleString(): void
    {
        $poolFactory = new PoolFactory(3, __DIR__);
        $subject = new PromiseWait($poolFactory);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $result = $subject->parallelMap($array, 'is_string');

        $expectedResult = [false, false, false, false, false, false, false, false, false];
        self::assertSame($result, $expectedResult);
    }

    /** @test */
    public function staticMethodAsString(): void
    {
        $poolFactory = new PoolFactory(3, __DIR__);
        $subject = new PromiseWait($poolFactory);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $result = $subject->parallelMap($array, TestClosure::class.'::addOne');

        $expectedResult = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        self::assertSame($result, $expectedResult);
    }

}
