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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Responsible for updating information associated with product price
 */
class UpdateProductPricesCommand
{
    /**
     * @var ProductId
     */
    private $productId;

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
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

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
     * @return UpdateProductPricesCommand
     */
    public function setPrice(string $price): self
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
     * @return UpdateProductPricesCommand
     */
    public function setEcotax(string $ecotax): self
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
     * @return UpdateProductPricesCommand
     */
    public function setTaxRulesGroupId(int $taxRulesGroupId): self
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
     * @return UpdateProductPricesCommand
     */
    public function setOnSale(bool $onSale): self
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
     * @return UpdateProductPricesCommand
     */
    public function setWholesalePrice(string $wholesalePrice): self
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
     * @return UpdateProductPricesCommand
     */
    public function setUnitPrice(string $unitPrice): self
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
     * @return UpdateProductPricesCommand
     */
    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }
}
