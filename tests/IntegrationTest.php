<?php

declare(strict_types=1);

namespace KnpU\LoremIpsumBundle\Tests;

use PHPUnit\Framework\TestCase;
use PP\ParallelBridge\PPParallelBridge;
use PP\ParallelBridge\PromiseWait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new ParallelBridgeTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $promiseWait = $container->get('pp_parallel_bridge.promise_wait');
        $this->assertInstanceOf(PromiseWait::class, $promiseWait);
    }
}

class ParallelBridgeTestingKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new PPParallelBridge(),
        ];
    }
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
