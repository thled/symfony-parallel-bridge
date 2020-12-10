<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Factory;

use Amp\Parallel\Worker\BootstrapWorkerFactory;
use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Pool;

class PoolFactory
{
    /** @var int */
    private $amphpMaxWorkers;

    /** @var string */
    private $projectDir;

    public function __construct(
        int $amphpMaxWorkers,
        string $projectDir
    ) {
        $this->amphpMaxWorkers = $amphpMaxWorkers;
        $this->projectDir = $projectDir;
    }

    public function create(): Pool
    {
        $workerBootStrapPath = $this->projectDir . '/config/worker-bootstrap.php';

        if(is_file($workerBootStrapPath)){
            $factory = new BootstrapWorkerFactory($workerBootStrapPath);
            return new DefaultPool($this->amphpMaxWorkers, $factory);
        }
        return new DefaultPool($this->amphpMaxWorkers);
    }
}
