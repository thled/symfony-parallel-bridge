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
        $elementToProcess = $element['element'];
        $callable = $element['callable'];
        $additionalParameters = $element['additionalParameters'];

        [$service, $functionName] = $callable;
        $serviceToCall = self::getContainer()->get(get_class($service));
        if (is_object($serviceToCall)) {
            return $serviceToCall->$functionName($elementToProcess, ...$additionalParameters);
        }

        return $callable($elementToProcess, ...$additionalParameters);
    }

    private static function getContainer(): ContainerInterface
    {
        return $GLOBALS['kernel']->getContainer();
    }
}
