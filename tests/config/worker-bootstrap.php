<?php

declare(strict_types=1);

use PP\ParallelBridge\Tests\ParallelBridgeTestingKernel;
use PP\ParallelBridge\Tests\TestService;

$kernel = new ParallelBridgeTestingKernel('test', true);
$kernel->boot();
$testService = new TestService();
$kernel->getContainer()->set(TestService::class, $testService);
$GLOBALS['kernel'] = $kernel;
