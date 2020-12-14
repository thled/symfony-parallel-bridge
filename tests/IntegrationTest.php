<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use Publicplan\ParallelBridge\Factory\PoolFactory;
use Publicplan\ParallelBridge\PromiseWait;

class IntegrationTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new ParallelBridgeTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $promiseWait = $container->get('publicplan_parallel_bridge.promise_wait');
        /** @phpstan-ignore-next-line */
        self::assertInstanceOf(PromiseWait::class, $promiseWait);
    }

    public function testServiceCallingAsync(): void
    {
        $testArray = [1, 2, 3, 4, 5, 6];
        $poolFactory = new PoolFactory(3, __DIR__);
        $promiseWait = new PromiseWait($poolFactory);
        $objectToCall = new TestService();
        $resultArray = $promiseWait->parallelMap($testArray, [$objectToCall, 'addTwo']);
        self::assertSame([3, 4, 5, 6, 7, 8], $resultArray);
    }
}
