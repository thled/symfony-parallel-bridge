<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use Publicplan\ParallelBridge\Factory\PoolFactory;
use Publicplan\ParallelBridge\PromiseWait;

class SyncProcessingTest extends TestCase
{
    public function testSyncProcessingWhenMaxWorkersIsZero(): void
    {
        $zeroWorkerPoolFactory = $this->createMock(PoolFactory::class);
        $zeroWorkerPoolFactory->expects(self::never())->method('create');
        $subject = new PromiseWait($zeroWorkerPoolFactory, 0);

        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $closure = TestClass::getClosure();

        $arg1 = 1;
        $arg2 = 1;

        $result = $subject->parallelMap($array, $closure, $arg1, $arg2);

        $expectedResult = [3, 4, 5, 6, 7, 8, 9, 10, 11];
        self::assertSame($result, $expectedResult);
    }
}
