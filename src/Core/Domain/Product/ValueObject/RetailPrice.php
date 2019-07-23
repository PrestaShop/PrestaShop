<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * This is the net sales price for your customers.
 * The retail price will automatically be calculated using the applied tax rate.
 */
final class RetailPrice
{
    /**
     * @var bool
     */
    private $displayOnSaleFlag;

    /**
     * @var TaxRuleId
     */
    private $taxRuleId;

    /**
     * @var Number
     */
    private $priceWithoutTax;

    /**
     * @param float $priceWithoutTax
     * @param int $taxRuleId
     * @param bool $displayOnSaleFlag
     *
     * @throws TaxRuleConstraintException
     * @throws ProductConstraintException
     */
    public function __construct(float $priceWithoutTax, int $taxRuleId, bool $displayOnSaleFlag)
    {
        try {
            $this->priceWithoutTax = (new Price($priceWithoutTax))->getValue();
        } catch (DomainConstraintException $e) {
            throw new ProductConstraintException(
                'Invalid products retail price',
                ProductConstraintException::INVALID_RETAIL_PRICE,
                $e
            );
        }

        $this->taxRuleId = new TaxRuleId($taxRuleId);
        $this->displayOnSaleFlag = $displayOnSaleFlag;
    }

    /**
     * @return bool
     */
    public function isDisplayOnSaleFlag(): bool
    {
        return $this->displayOnSaleFlag;
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }

    /**
     * @return Number
     */
    public function getPriceWithoutTax(): Number
    {
        return $this->priceWithoutTax;
    }
}
