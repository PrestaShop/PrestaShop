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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * DTO for product that was found by search
 */
class FoundProduct
{
    /**
     * @var bool
     */
    private $availableOutOfStock;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $taxRate;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @var float
     */
    private $priceTaxIncl;

    /**
     * @var float
     */
    private $priceTaxExcl;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var string
     */
    private $location;

    /**
     * @var ProductCombination[]
     */
    private $combinations;

    /**
     * @var ProductCustomizationField[]
     */
    private $customizationFields;

    /**
     * @param int $productId
     * @param string $name
     * @param string $formattedPrice
     * @param float $priceTaxIncl
     * @param float $priceTaxExcl
     * @param float $taxRate
     * @param int $stock
     * @param string $location
     * @param bool $availableOutOfStock
     * @param ProductCombination[] $combinations
     * @param ProductCustomizationField[] $customizationFields
     */
    public function __construct(
        int $productId,
        string $name,
        string $formattedPrice,
        float $priceTaxIncl,
        float $priceTaxExcl,
        float $taxRate,
        int $stock,
        string $location,
        bool $availableOutOfStock,
        array $combinations = [],
        array $customizationFields = []
    ) {
        $this->productId = $productId;
        $this->name = $name;
        $this->formattedPrice = $formattedPrice;
        $this->priceTaxIncl = $priceTaxIncl;
        $this->priceTaxExcl = $priceTaxExcl;
        $this->taxRate = $taxRate;
        $this->stock = $stock;
        $this->location = $location;
        $this->availableOutOfStock = $availableOutOfStock;
        $this->combinations = $combinations;
        $this->customizationFields = $customizationFields;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @return float
     */
    public function getPriceTaxIncl(): float
    {
        return $this->priceTaxIncl;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcl(): float
    {
        return $this->priceTaxExcl;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @return ProductCombination[]
     */
    public function getCombinations(): array
    {
        return $this->combinations;
    }

    /**
     * @return ProductCustomizationField[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }

    /**
     * @return bool
     */
    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }
}
