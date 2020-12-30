<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Amp\MultiReasonException;

interface PromiseWaitInterface
{
    /**
     * @param array<mixed> $arrayToMap
     * @param mixed $args
     *
     * @throws MultiReasonException
     *
     * @return array<mixed>
     */
    public function parallelMap(array $arrayToMap, callable $callable, ...$args): array;
}
