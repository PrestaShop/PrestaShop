<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\RetailPrice;

class RetailPriceTest extends TestCase
{
    public function testItDoesNotAllowNegativeValues(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_RETAIL_PRICE);

        $negativeValue = -0.1;

        new RetailPrice(
            $negativeValue,
            1,
            false
        );
    }
}
