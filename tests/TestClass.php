<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

class TestClass extends ParentTestClass
{
    public static function getClosure(): \Closure
    {
        return static function (int $input): int {
            return $input + 1;
        };
    }

    public static function addOne(int $number): int
    {
        return $number + 1;
    }

    public static function getNumber(int $int): int
    {
        return $int + 10;
    }

    public function __invoke(int $number)
    {
        return $number + 3;
    }
}
