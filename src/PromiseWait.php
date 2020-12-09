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
     * @return array<mixed>
     */
    public function parallelMap(array $arrayToRemap, string $service, string $function): array
    {
        $mappedArray = [];
        $preparedMappedArray = $this->remapArray($arrayToRemap, $service, $function);
        try {
            /** @phpstan-ignore-next-line */
            $mappedArray = Promise\wait(
                parallelMap(
                    $preparedMappedArray,
                    [ServiceCaller::class, 'processSingleElement'],
                    $this->poolFactory->create(),
                )
            );
        } catch (MultiReasonException $e) {
            dd($e->getReasons()); //todo:: dont die do something more useful
        }
        return $mappedArray;
    }

    private function remapArray(array $array, string $service, string $function): array
    {
        $newArray = [];
        foreach ($array as $element) {
            $cool = [];
            $cool['element'] = $element;
            $cool['service'] = $service;
            $cool['function'] = $function;
            $newArray[] = $cool;
        }
        return $newArray;
    }
}
