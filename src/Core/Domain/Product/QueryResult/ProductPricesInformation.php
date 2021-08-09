<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Holds information about product prices
 */
class ProductPricesInformation
{
    /**
     * @var DecimalNumber
     */
    private $price;

    /**
     * @var DecimalNumber
     */
    private $priceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $ecotax;

    /**
     * @var int
     */
    private $taxRulesGroupId;

    /**
     * @var bool
     */
    private $onSale;

    /**
     * @var DecimalNumber
     */
    private $wholesalePrice;

    /**
     * @var DecimalNumber
     */
    private $unitPrice;

    /**
     * @var string
     */
    private $unity;

    /**
     * @var DecimalNumber
     */
    private $unitPriceRatio;

    /**
     * @param DecimalNumber $price
     * @param DecimalNumber $priceTaxIncluded
     * @param DecimalNumber $ecotax
     * @param int $taxRulesGroupId
     * @param bool $onSale
     * @param DecimalNumber $wholesalePrice
     * @param DecimalNumber $unitPrice
     * @param string $unity
     * @param DecimalNumber $unitPriceRatio
     */
    public function __construct(
        DecimalNumber $price,
        DecimalNumber $priceTaxIncluded,
        DecimalNumber $ecotax,
        int $taxRulesGroupId,
        bool $onSale,
        DecimalNumber $wholesalePrice,
        DecimalNumber $unitPrice,
        string $unity,
        DecimalNumber $unitPriceRatio
    ) {
        $this->price = $price;
        $this->priceTaxIncluded = $priceTaxIncluded;
        $this->ecotax = $ecotax;
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->onSale = $onSale;
        $this->wholesalePrice = $wholesalePrice;
        $this->unitPrice = $unitPrice;
        $this->unity = $unity;
        $this->unitPriceRatio = $unitPriceRatio;
    }

    /**
     * @return DecimalNumber
     */
    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    /**
     * @return DecimalNumber
     */
    public function getPriceTaxIncluded(): DecimalNumber
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getEcotax(): DecimalNumber
    {
        return $this->ecotax;
    }

    /**
     * @return int
     */
    public function getTaxRulesGroupId(): int
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return bool
     */
    public function isOnSale(): bool
    {
        return $this->onSale;
    }

    /**
     * @return DecimalNumber
     */
    public function getWholesalePrice(): DecimalNumber
    {
        return $this->wholesalePrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getUnitPrice(): DecimalNumber
    {
        return $this->unitPrice;
    }

    /**
     * @return string
     */
    public function getUnity(): string
    {
        return $this->unity;
    }

    /**
     * @return DecimalNumber
     */
    public function getUnitPriceRatio(): DecimalNumber
    {
        return $this->unitPriceRatio;
    }
}
