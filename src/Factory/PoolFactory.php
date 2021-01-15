<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Factory;

use Amp\Parallel\Worker\BootstrapWorkerFactory;
use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Pool;

class PoolFactory
{
    /** @var string */
    private $projectDir;

    public function __construct(
        string $projectDir
    ) {
        $this->projectDir = $projectDir;
    }

    public function create(int $amphpMaxWorkers): Pool
    {
        $workerBootStrapPath = $this->projectDir . '/config/worker-bootstrap.php';

        if (\is_file($workerBootStrapPath)) {
            $factory = new BootstrapWorkerFactory($workerBootStrapPath);
            return new DefaultPool($amphpMaxWorkers, $factory);
        }
        return new DefaultPool($amphpMaxWorkers);
    }
}
