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
        $factory = new BootstrapWorkerFactory($this->projectDir . '/config/worker-bootstrap.php');

        return new DefaultPool($this->amphpMaxWorkers, $factory);
    }
}
