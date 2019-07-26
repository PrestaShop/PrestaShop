<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\UnitPrice;

class UnitPriceTest extends TestCase
{
    public function testItDoesNotAllowNegativeValues(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_UNIT_PRICE);

        $negativeValue = -0.1;

        new UnitPrice(
            $negativeValue,
            'per kilo'
        );
    }
}
