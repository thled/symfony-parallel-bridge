<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Amp\MultiReasonException;
use Amp\Parallel\Sync\SerializationException;
use function Amp\ParallelFunctions\parallelMap;
use Amp\Promise;
use Opis\Closure\SerializableClosure;

use Publicplan\ParallelBridge\Factory\PoolFactory;

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

        $arrayToRemap = $this->remapArray($arrayToRemap, $callable, $args);
        $callable = [ServiceCaller::class, 'processSingleElement'];

        /** @phpstan-ignore-next-line */
        return Promise\wait(
            parallelMap(
                $arrayToRemap,
                $callable,
                $this->poolFactory->create(),
            )
        );
    }

    private function remapArray(array $array, string $serializedCallable, array $additionalParameters): array
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
}
