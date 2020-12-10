<?php

declare(strict_types=1);

namespace PP\ParallelBridge;

use Psr\Container\ContainerInterface;

class ServiceCaller
{
    /**
     * @param array{"service": string,"function":string, "element": mixed} $mappedNrwService
     *
     * @return mixed
     */
    public static function processSingleElement(array $element)
    {
        $serviceName = $element['service'];
        $functionName = $element['function'];
        $elementToProcess = $element['element'];
        $additionalParameters = $element['additionalParameters'];
        $serviceToCall = self::getContainer()->get($serviceName);

        return $serviceToCall->$functionName($elementToProcess, ...$additionalParameters);
    }

    private static function getContainer(): ContainerInterface
    {
        return $GLOBALS['kernel']->getContainer();
    }
}
