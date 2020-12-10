<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;
use PP\ParallelBridge\Factory\PoolFactory;
use PP\ParallelBridge\PPParallelBridge;
use PP\ParallelBridge\PromiseWait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class IntegrationTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new ParallelBridgeTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $promiseWait = $container->get('pp_parallel_bridge.promise_wait');
        $this->assertInstanceOf(PromiseWait::class, $promiseWait);
    }

    public function testServiceCallingAsync(): void
    {
        $kernel = new ParallelBridgeTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $promiseWait = $container->get('pp_parallel_bridge.promise_wait');

        /** @var PromiseWait */
        $testArray = [1, 2, 3, 4, 5, 6];
        $poolFactory = new PoolFactory(3, __DIR__);
        $promiseWait = new PromiseWait($poolFactory);
        $resultArray = $promiseWait->parallelMap($testArray, [TestService::class, 'addTwo']);
        self::assertSame([3, 4, 5, 6, 7, 8], $resultArray);
    }
}

