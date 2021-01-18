<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Amp\MultiReasonException;
use Amp\Parallel\Sync\SerializationException;
use function Amp\ParallelFunctions\parallelMap;
use Amp\Promise;
use Opis\Closure\SerializableClosure;
use Publicplan\ParallelBridge\Factory\PoolFactory;

use Publicplan\ParallelBridge\Model\PackedArguments;

class PromiseWait implements PromiseWaitInterface
{
    private const PROCESS_SINGLE_ELEMENT = [ServiceCaller::class, 'processSingleElement'];

    /** @var PoolFactory */
    private $poolFactory;

    /** @var int */
    private $amphpMaxWorkers;

    public function __construct(
        PoolFactory $poolFactory,
        int $amphpMaxWorkers
    ) {
        $this->poolFactory = $poolFactory;
        $this->amphpMaxWorkers = $amphpMaxWorkers;
    }

    /**
     * @param array<mixed> $arrayToRemap
     * @param mixed $args
     *
     * @throws SerializationException
     * @throws MultiReasonException
     *
     * @return array<mixed, mixed>
     */
    public function parallelMap(array $arrayToRemap, callable $callable, ...$args): array
    {
        if ($this->amphpMaxWorkers === 0) {
            return $this->syncMap($arrayToRemap, $callable, $args);
        }

        return $this->asyncMap($arrayToRemap, $callable, $args);
    }

    /**
     * @param array<mixed> $array
     * @param array<mixed> $additionalParameters
     *
     * @return array<int, PackedArguments>
     */
    private function packParametersToArray(array $array, string $serializedCallable, array $additionalParameters): array
    {
        $newArray = [];
        foreach ($array as $element) {
            $newArray[] = new PackedArguments($element, $serializedCallable, $additionalParameters);
        }

        return $newArray;
    }

    /**
     * @param array<mixed> $arrayToRemap
     * @param array<mixed> $args
     *
     * @return array<mixed, mixed>
     */
    private function syncMap(array $arrayToRemap, callable $callable, array $args): array
    {
        $resultArray = [];
        foreach ($arrayToRemap as $key => $value) {
            $resultArray[$key] = $callable($value, ...$args);
        }

        return $resultArray;
    }

    /**
     * @param array<mixed> $arrayToRemap
     * @param array<mixed> $args
     *
     * @return array<mixed, mixed>
     */
    private function asyncMap(array $arrayToRemap, callable $callable, array $args): array
    {
        $callable = $this->convertObjectsInCallablesToClassnames($callable);

        $serializedCallable = $this->serializeCallable($callable);
        $packedArray = $this->packParametersToArray($arrayToRemap, $serializedCallable, $args);

        return Promise\wait(
            parallelMap(
                $packedArray,
                self::PROCESS_SINGLE_ELEMENT,
                $this->poolFactory->create($this->amphpMaxWorkers),
            )
        );
    }

    /** @return callable|array<int, string> */
    private function convertObjectsInCallablesToClassnames(callable $callable)
    {
        if (\is_array($callable) && \is_object($callable[0])) {
            $class = \get_class($callable[0]);
            $function = (string)$callable[1];
            $callable = [$class, $function];
        }
        return $callable;
    }

    /**
     * @param callable|array<int, string> $callable
     *
     * @throws SerializationException
     */
    private function serializeCallable($callable): string
    {
        $serializableCallable = $callable;
        if ($callable instanceof \Closure) {
            $serializableCallable = new SerializableClosure($callable);
        }

        try {
            $serializedCallable = \serialize($serializableCallable);
        } catch (\Throwable $e) {
            throw new SerializationException('Unsupported callable: ' . $e->getMessage(), 0, $e);
        }
        return $serializedCallable;
    }
}
