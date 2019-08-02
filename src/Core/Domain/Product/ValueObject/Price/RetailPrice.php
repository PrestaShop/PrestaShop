<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price;

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
class RetailPrice
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
