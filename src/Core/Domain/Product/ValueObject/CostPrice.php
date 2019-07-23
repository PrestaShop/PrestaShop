<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * The price that the product actually costs - used for margin calculation etc...
 */
final class CostPrice
{
    /**
     * @var Number
     */
    private $price;

    /**
     * @param float $price
     *
     * @throws ProductConstraintException
     */
    public function __construct(float $price)
    {
        try {
            $numberPrice = (new Price($price))->getValue();
        } catch (DomainConstraintException $e) {
            throw new ProductConstraintException(
                'invalid cost price',
                ProductConstraintException::INVALID_COST_PRICE,
                $e
            );
        }

        $this->price = $numberPrice;
    }

    /**
     * @return Number
     */
    public function getValue(): Number
    {
        return $this->price;
    }
}
