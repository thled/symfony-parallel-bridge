<?php

declare(strict_types=1);

namespace PP\ParallelBridge\Tests;

use PHPUnit\Framework\TestCase;

final class PromiseWaitTest extends TestCase
{
    /** @test */
    public function nothing(): void
    {
        self::assertTrue(true);
    }
}
