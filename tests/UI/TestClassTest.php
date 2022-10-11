<?php

declare(strict_types=1);

namespace Tests\UI;

use PHPUnit\Framework\TestCase;

class TestClassTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        $this->assertFalse(false);
    }
}
