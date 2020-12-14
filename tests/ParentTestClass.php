<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

class ParentTestClass
{
    public static function getNumber(int $int, int $arg1, int $arg2): int
    {
        return $int + 5 + $arg1 + $arg2;
    }
}
