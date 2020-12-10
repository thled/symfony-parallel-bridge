<?php

declare(strict_types=1);

namespace PP\ParallelBridge;

use Psr\Container\ContainerInterface;
use Opis\Closure\SerializableClosure;

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
        $callable = unserialize($element['callable']);
        $additionalParameters = $element['additionalParameters'];

        if($callable instanceof SerializableClosure){
            $callable = $callable->getClosure();
        }

        if(is_array($callable)){
            [$service, $functionName] = $callable;
            if(self::getContainer()->has($service)){
                $serviceToCall = self::getContainer()->get($service);
                return $serviceToCall->$functionName($elementToProcess, ...$additionalParameters);
            }
        }

        return $callable($elementToProcess, ...$additionalParameters);
    }

    private static function getContainer(): ContainerInterface
    {
        return $GLOBALS['kernel']->getContainer();
    }
}
