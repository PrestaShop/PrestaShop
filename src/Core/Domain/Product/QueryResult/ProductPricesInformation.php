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

use PrestaShop\Decimal\Number;

/**
 * Holds information about product prices
 */
class ProductPricesInformation
{
    /**
     * @var \PrestaShop\Decimal\Number
     */
    private $price;

    /**
     * @var \PrestaShop\Decimal\Number
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
     * @var \PrestaShop\Decimal\Number
     */
    private $wholesalePrice;

    /**
     * @var \PrestaShop\Decimal\Number
     */
    private $unitPrice;

    /**
     * @var string
     */
    private $unity;

    /**
     * @var \PrestaShop\Decimal\Number
     */
    private $unitPriceRatio;

    /**
     * @param \PrestaShop\Decimal\Number $price
     * @param \PrestaShop\Decimal\Number $ecotax
     * @param int $taxRulesGroupId
     * @param bool $onSale
     * @param \PrestaShop\Decimal\Number $wholesalePrice
     * @param \PrestaShop\Decimal\Number $unitPrice
     * @param string $unity
     * @param \PrestaShop\Decimal\Number $unitPriceRatio
     */
    public function __construct(
        Number $price,
        Number $ecotax,
        int $taxRulesGroupId,
        bool $onSale,
        Number $wholesalePrice,
        Number $unitPrice,
        string $unity,
        Number $unitPriceRatio
    ) {
        $this->price = $price;
        $this->ecotax = $ecotax;
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->onSale = $onSale;
        $this->wholesalePrice = $wholesalePrice;
        $this->unitPrice = $unitPrice;
        $this->unity = $unity;
        $this->unitPriceRatio = $unitPriceRatio;
    }

    /**
     * @return \PrestaShop\Decimal\Number
     */
    public function getPrice(): Number
    {
        return $this->price;
    }

    /**
     * @return \PrestaShop\Decimal\Number
     */
    public function getEcotax(): Number
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
     * @return \PrestaShop\Decimal\Number
     */
    public function getWholesalePrice(): Number
    {
        return $this->wholesalePrice;
    }

    /**
     * @return \PrestaShop\Decimal\Number
     */
    public function getUnitPrice(): Number
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
     * @return \PrestaShop\Decimal\Number
     */
    public function getUnitPriceRatio(): Number
    {
        return $this->unitPriceRatio;
    }
}
