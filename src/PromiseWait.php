<?php

declare(strict_types=1);

namespace PP\ParallelBridge;

use Amp\MultiReasonException;
use Amp\Promise;
use PP\ParallelBridge\Factory\PoolFactory;

use function Amp\ParallelFunctions\parallelMap;

class PromiseWait
{
    /** @var PoolFactory */
    private $poolFactory;

    public function __construct(PoolFactory $poolFactory)
    {
        $this->poolFactory = $poolFactory;
    }

    /**
     * @param array<mixed> $arrayToRemap
     *
     * @return array<mixed>
     * @throws MultiReasonException
     */
    public function parallelMap(array $arrayToRemap, callable $callable, ...$additionalParameters): array
    {
        $preparedMappedArray = $this->remapArray($arrayToRemap, $callable, $additionalParameters);

        /** @phpstan-ignore-next-line */
        return Promise\wait(
            parallelMap(
                $preparedMappedArray,
                [ServiceCaller::class, 'processSingleElement'],
                $this->poolFactory->create(),
            )
        );
    }

    private function remapArray(array $array, callable $callable, array $additionalParameters): array
    {
        $newArray = [];
        foreach ($array as $element) {
            $cool = [];
            $cool['element'] = $element;
            $cool['callable'] = $callable;
            $cool['additionalParameters'] = $additionalParameters;
            $newArray[] = $cool;
        }
        return $newArray;
    }
}
