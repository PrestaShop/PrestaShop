<?php

namespace Tests\Unit\PrestaShopBundle;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Version;

class AppKernelTest extends TestCase
{
    public function testVersion(): void
    {
        self::assertSame(\AppKernel::VERSION, Version::VERSION);
    }
}
