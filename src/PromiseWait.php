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
    private const CALLABLE = [ServiceCaller::class, 'processSingleElement'];
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
     * @throws MultiReasonException
     *
     * @return array<mixed>
     */
    public function parallelMap(array $arrayToRemap, callable $callable, ...$args): array
    {
        if (\is_array($callable) && \is_object($callable[0])) {
            $class = \get_class($callable[0]);
            $function = $callable[1];
            $callable = [$class, $function];
        }

        if ($callable instanceof \Closure) {
            $callable = new SerializableClosure($callable);
        }

        try {
            $callable = \serialize($callable);
        } catch (\Throwable $e) {
            throw new SerializationException('Unsupported callable: ' . $e->getMessage(), 0, $e);
        }

        $packedArray = $this->packParametersToArray($arrayToRemap, $callable, $args);

        if ($this->amphpMaxWorkers === 0) {
            return $this->remapSync($packedArray);
        }
        return $this->remapAsync($packedArray);
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
     * @param array<PackedArguments> $packedArray
     *
     * @return array<int, mixed>
     */
    private function remapSync(array $packedArray): array
    {
        $callable = self::CALLABLE;
        $resultArray = [];
        foreach ($packedArray as $key => $value) {
            $resultArray[$key] = $callable($value);
        }

        return $resultArray;
    }

    /**
     * @param array<PackedArguments> $packedArray
     *
     * @return array<mixed>
     */
    private function remapAsync(array $packedArray): array
    {
        return Promise\wait(
            parallelMap(
                $packedArray,
                self::CALLABLE,
                $this->poolFactory->create($this->amphpMaxWorkers),
            )
        );
    }
}
