<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\Tests;

class ParentTestClass
{
    public static function getNumber(int $int): int
    {
        return $int + 5;
    }
}
