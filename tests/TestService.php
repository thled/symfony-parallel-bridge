<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

use Serializable;

class TestService implements Serializable
{
    public function serialize(): ?string
    {
        throw new \LogicException('This is not a serializable Class :-)');
    }

    /** @param string $serialized */
    public function unserialize($serialized): void
    {
        throw new \LogicException('This is not a serializable Class :-)');
    }

    public function addTwo(int $number): int
    {
        return $this->add($number, 2);
    }

    private function add(int $number, int $addAmount): int
    {
        return $number + $addAmount;
    }
}
