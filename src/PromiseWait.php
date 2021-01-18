<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Amp\MultiReasonException;
use Amp\Parallel\Sync\SerializationException;
use function Amp\ParallelFunctions\parallelMap;
use Amp\Promise;
use Opis\Closure\SerializableClosure;

use Publicplan\ParallelBridge\Factory\PoolFactory;

class PromiseWait implements PromiseWaitInterface
{
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
        $processSingleElement = [ServiceCaller::class, 'processSingleElement'];

        if ($this->amphpMaxWorkers === 0) {
            return $this->remapSync($packedArray, $processSingleElement);
        }
        return $this->remapAsync($packedArray, $processSingleElement);
    }

    /**
     * @param array<mixed> $array
     * @param array<mixed> $additionalParameters
     *
     * @return array<int, array<string, mixed>>
     */
    private function packParametersToArray(array $array, string $serializedCallable, array $additionalParameters): array
    {
        $newArray = [];
        foreach ($array as $element) {
            $newArray[] = [
                'element' => $element,
                'callable' => $serializedCallable,
                'additionalParameters' => $additionalParameters,
            ];
        }

        return $newArray;
    }

    /**
     * @param array<int, array<string, mixed>> $packedArray
     *
     * @return array<int, mixed>
     */
    private function remapSync(array $packedArray, callable $processSingleElement): array
    {
        $resultArray = [];
        foreach ($packedArray as $key => $value) {
            $resultArray[$key] = $processSingleElement($value);
        }

        return $resultArray;
    }

    /**
     * @param array<mixed> $packedArray
     *
     * @return array<mixed>
     */
    private function remapAsync(array $packedArray, callable $processSingleElement): array
    {
        return Promise\wait(
            parallelMap(
                $packedArray,
                $processSingleElement,
                $this->poolFactory->create($this->amphpMaxWorkers),
            )
        );
    }
}
