<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Psr\Container\ContainerInterface;
use Opis\Closure\SerializableClosure;
use TypeError;

class ServiceCaller
{
    /**
     * @param array{"service": string,"function":string, "element": mixed} $mappedNrwService
     *
     * @return mixed
     */
    public static function processSingleElement(array $packedParamArray)
    {
        $elementToProcess = $packedParamArray['element'];
        $callable = unserialize($packedParamArray['callable'], [true]);
        $additionalParameters = $packedParamArray['additionalParameters'];

        if ($callable instanceof SerializableClosure) {
            $callable = $callable->getClosure();
        }

        if (is_array($callable)) {
            [$service, $functionName] = $callable;
            if (self::getContainer()->has($service)) {
                $service = self::getContainer()->get($service);
            }
            $callable = [$service, $functionName];
        }
        try {
            /** @noinspection VariableFunctionsUsageInspection */
            return call_user_func($callable, $elementToProcess, ...$additionalParameters);
        } catch (TypeError $exception) {
            $message = $exception->getMessage();
            $message .= ' Maybe you have forgotten to make your service public?';
            throw new TypeError($message, $exception->getCode(), $exception);
        }
    }

    private static function getContainer(): ContainerInterface
    {
        return $GLOBALS['kernel']->getContainer();
    }
}
