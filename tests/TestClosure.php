<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

class TestClosure
{
    public static function getClosure(): \Closure
    {
        return static function (int $input): int {
            return $input + 1;
        };
    }
}
