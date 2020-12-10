<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

class TestService
{
    public function addTwo(int $number): int
    {
        return $this->add($number, 2);
    }

    private function add(int $number, int $addAmount): int
    {
        return $number + $addAmount;
    }
}
