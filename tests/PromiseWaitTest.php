<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use PP\ParallelBridge\Factory\PoolFactory;
use PP\ParallelBridge\PromiseWait;

final class PromiseWaitTest extends TestCase
{
    /** @test */
    public function closure(): void
    {
        $poolFactory = new PoolFactory(3,'/app');
        $subject = new PromiseWait($poolFactory);

        $array = [0,1,2,3,4,5,6,7,8];
        $closure = TestClosure::getClosure();

        $result = $subject->parallelMap($array, $closure);

        $expectedResult = [1,2,3,4,5,6,7,8,9];
        self::assertSame($result, $expectedResult);
    }
}
