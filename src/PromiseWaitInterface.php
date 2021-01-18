<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Amp\MultiReasonException;

interface PromiseWaitInterface
{
    /**
     * @param array<mixed> $arrayToRemap
     * @param mixed $args
     *
     * @return array<mixed>
     * @throws MultiReasonException
     */
    public function parallelMap(array $arrayToRemap, callable $callable, ...$args): array;
}
