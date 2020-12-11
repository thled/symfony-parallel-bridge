<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

use Publicplan\ParallelBridge\PublicplanParallelBridgeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class ParallelBridgeTestingKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new PublicplanParallelBridgeBundle(),
        ];
    }
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
