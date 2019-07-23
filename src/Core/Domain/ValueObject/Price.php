<?php

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Unsigned price value.
 */
final class Price
{
    /**
     * @var Number
     */
    private $price;

    /**
     * @param float $price
     *
     * @throws DomainConstraintException
     */
    public function __construct(float $price)
    {
        $priceAsNumber = new Number((string) $price);

        $this->assertIsLargerThenZero($priceAsNumber);

        $this->price = $priceAsNumber;

    }

    /**
     * @return Number
     */
    public function getValue(): Number
    {
        return $this->price;
    }

    /**
     * @param Number $price
     *
     * @throws DomainConstraintException
     */
    private function assertIsLargerThenZero(Number $price): void
    {
        $zeroNumber = new Number('0');

        if ($price->isLowerThan($zeroNumber)) {
            throw new DomainConstraintException(
                sprintf('Expected price "%s" to be more then zero', $price->__toString()),
                DomainConstraintException::INVALID_PRICE
            );
        }
    }
}
