<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

class TestClass extends ParentTestClass
{
    public static function getClosure(): \Closure
    {
        return static function (int $input, int $arg1, int $arg2): int {
            return $input + 1 + $arg1 + $arg2;
        };
    }

    public static function addOne(int $number, int $arg1, int $arg2): int
    {
        return $number + 1 + $arg1 + $arg2;
    }

    public static function getNumber(int $int, int $arg1, int $arg2): int
    {
        return $int + 10 + $arg1 + $arg2;
    }

    public function __invoke(int $number, int $arg1, int $arg2): int
    {
        return $number + 3 + $arg1 + $arg2;
    }
}
