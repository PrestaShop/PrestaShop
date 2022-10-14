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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput;

use PrestaShop\Decimal\DecimalNumber;

class ProductPricesInput
{
    /**
     * @var DecimalNumber|null
     */
    private $price;

    /**
     * @var DecimalNumber|null
     */
    private $ecotax;

    /**
     * @var int|null
     */
    private $taxRulesGroupId;

    /**
     * @var bool|null
     */
    private $onSale;

    /**
     * @var DecimalNumber|null
     */
    private $wholesalePrice;

    /**
     * @var DecimalNumber|null
     */
    private $unitPrice;

    /**
     * @var string|null
     */
    private $unity;

    /**
     * @return DecimalNumber|null
     */
    public function getPrice(): ?DecimalNumber
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return ProductPricesInput
     */
    public function setPrice(string $price): ProductPricesInput
    {
        $this->price = new DecimalNumber($price);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getEcotax(): ?DecimalNumber
    {
        return $this->ecotax;
    }

    /**
     * @param string $ecotax
     *
     * @return ProductPricesInput
     */
    public function setEcotax(string $ecotax): ProductPricesInput
    {
        $this->ecotax = new DecimalNumber($ecotax);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTaxRulesGroupId(): ?int
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @param int $taxRulesGroupId
     *
     * @return ProductPricesInput
     */
    public function setTaxRulesGroupId(int $taxRulesGroupId): ProductPricesInput
    {
        $this->taxRulesGroupId = $taxRulesGroupId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    /**
     * @param bool $onSale
     *
     * @return ProductPricesInput
     */
    public function setOnSale(bool $onSale): ProductPricesInput
    {
        $this->onSale = $onSale;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getWholesalePrice(): ?DecimalNumber
    {
        return $this->wholesalePrice;
    }

    /**
     * @param string $wholesalePrice
     *
     * @return ProductPricesInput
     */
    public function setWholesalePrice(string $wholesalePrice): ProductPricesInput
    {
        $this->wholesalePrice = new DecimalNumber($wholesalePrice);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getUnitPrice(): ?DecimalNumber
    {
        return $this->unitPrice;
    }

    /**
     * @param string $unitPrice
     *
     * @return ProductPricesInput
     */
    public function setUnitPrice(string $unitPrice): ProductPricesInput
    {
        $this->unitPrice = new DecimalNumber($unitPrice);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUnity(): ?string
    {
        return $this->unity;
    }

    /**
     * @param string $unity
     *
     * @return ProductPricesInput
     */
    public function setUnity(string $unity): ProductPricesInput
    {
        $this->unity = $unity;

        return $this;
    }
}
