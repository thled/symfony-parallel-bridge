<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Factory;

use Amp\Parallel\Worker\BootstrapWorkerFactory;
use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Pool;

class TestPoolFactory
{
    /** @var int */
    private $amphpMaxWorkers;

    public function __construct(
        int $amphpMaxWorkers
    ) {
        $this->amphpMaxWorkers = $amphpMaxWorkers;
    }

    public function create(): Pool
    {
        $workerBootStrapPath = __DIR__ .'/test-worker-bootstrap.php';

        if(is_file($workerBootStrapPath)){
            $factory = new BootstrapWorkerFactory($workerBootStrapPath);
            return new DefaultPool($this->amphpMaxWorkers, $factory);
        }
        return new DefaultPool($this->amphpMaxWorkers);
    }
}
