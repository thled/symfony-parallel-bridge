<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

use PP\ParallelBridge\PPParallelBridge;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

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
