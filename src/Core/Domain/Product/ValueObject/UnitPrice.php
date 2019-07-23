<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Price per unit - e.g 10 per kilo.
 */
final class UnitPrice
{
    /**
     * @var Number
     */
    private $price;

    /**
     * @var string
     */
    private $unit;

    /**
     * @param float $price
     * @param string $unit
     *
     * @throws ProductConstraintException
     */
    public function __construct(float $price, string $unit)
    {
        $this->price = new Number((string) $price);

        $this->assertIsLargerThenZero($this->price);

        $this->unit = $unit;
    }

    /**
     * @return Number
     */
    public function getPrice(): Number
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param Number $price
     *
     * @throws ProductConstraintException
     */
    private function assertIsLargerThenZero(Number $price): void
    {
        $zeroNumber = new Number('0');

        if ($price->isLowerThan($zeroNumber)) {
            throw new ProductConstraintException(
                'Unit price should be more then zero',
                ProductConstraintException::INVALID_UNIT_PRICE
            );
        }
    }
}
