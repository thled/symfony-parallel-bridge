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
        $serviceToCall = self::getContainer()->get($serviceName);

        return $serviceToCall->$functionName($elementToProcess);
    }

    private static function getContainer(): ContainerInterface
    {
        return $GLOBALS['kernel']->getContainer();
    }
}
