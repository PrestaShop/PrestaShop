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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Holds information of combination prices
 */
class CombinationPrices
{
    /**
     * @var DecimalNumber
     */
    private $ecoTax;

    /**
     * @var DecimalNumber
     */
    private $impactOnPrice;

    /**
     * @var DecimalNumber
     */
    private $impactOnPriceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $impactOnUnitPrice;

    /**
     * @var DecimalNumber
     */
    private $wholesalePrice;

    /**
     * @param DecimalNumber $ecoTax
     * @param DecimalNumber $impactOnPrice
     * @param DecimalNumber $impactOnPriceTaxIncluded
     * @param DecimalNumber $impactOnUnitPrice
     * @param DecimalNumber $wholesalePrice
     */
    public function __construct(
        DecimalNumber $ecoTax,
        DecimalNumber $impactOnPrice,
        DecimalNumber $impactOnPriceTaxIncluded,
        DecimalNumber $impactOnUnitPrice,
        DecimalNumber $wholesalePrice
    ) {
        $this->ecoTax = $ecoTax;
        $this->impactOnPrice = $impactOnPrice;
        $this->impactOnPriceTaxIncluded = $impactOnPriceTaxIncluded;
        $this->impactOnUnitPrice = $impactOnUnitPrice;
        $this->wholesalePrice = $wholesalePrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getEcoTax(): DecimalNumber
    {
        return $this->ecoTax;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnPrice(): DecimalNumber
    {
        return $this->impactOnPrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnPriceTaxIncluded(): DecimalNumber
    {
        return $this->impactOnPriceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnUnitPrice(): DecimalNumber
    {
        return $this->impactOnUnitPrice;
    }

    /**
     * @return DecimalNumber
     */
    public function getWholesalePrice(): DecimalNumber
    {
        return $this->wholesalePrice;
    }
}
