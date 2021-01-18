<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Model;

class PackedArguments
{
    /** @var mixed */
    private $element;

    /** @var string */
    private $serializedCallable;

    /** @var array<mixed> */
    private $additionalParameters;

    /**
     * @param mixed $element
     * @param array<mixed> $additionalParameters
     */
    public function __construct($element, string $serializedCallable, array $additionalParameters)
    {
        $this->element = $element;
        $this->serializedCallable = $serializedCallable;
        $this->additionalParameters = $additionalParameters;
    }

    /** @return mixed */
    public function getElement()
    {
        return $this->element;
    }

    public function getSerializedCallable(): string
    {
        return $this->serializedCallable;
    }

    /** @return array<mixed> */
    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }
}
