<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge;

use Opis\Closure\SerializableClosure;
use Psr\Container\ContainerInterface;
use Publicplan\ParallelBridge\Model\PackedArguments;
use TypeError;

class ServiceCaller
{
    /** @return mixed */
    public static function processSingleElement(PackedArguments $packedArguments)
    {
        $elementToProcess = $packedArguments->getElement();
        $callable = \unserialize($packedArguments->getSerializedCallable(), [true]);
        $additionalParameters = $packedArguments->getAdditionalParameters();

        if ($callable instanceof SerializableClosure) {
            $callable = $callable->getClosure();
        }

        if (\is_array($callable)) {
            [$service, $functionName] = $callable;
            if (\is_string($service) && self::getContainer()->has($service)) {
                $service = self::getContainer()->get($service);
            }
            $callable = [$service, $functionName];
        }
        try {
            /** @noinspection VariableFunctionsUsageInspection */
            return \call_user_func($callable, $elementToProcess, ...$additionalParameters);
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
