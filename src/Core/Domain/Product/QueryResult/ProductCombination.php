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

/**
 * Holds product combination data
 */
class ProductCombination
{
    /**
     * @var int
     */
    private $attributeCombinationId;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var float
     */
    private $priceTaxExcluded;

    /**
     * @var float
     */
    private $priceTaxIncluded;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @param int $attributeCombinationId
     * @param string $attribute
     * @param int $stock
     * @param string $formattedPrice
     * @param float $priceTaxExcluded
     * @param float $priceTaxIncluded
     * @param string $location
     * @param string $reference
     */
    public function __construct(
        int $attributeCombinationId,
        string $attribute,
        int $stock,
        string $formattedPrice,
        float $priceTaxExcluded,
        float $priceTaxIncluded,
        string $location,
        string $reference
    ) {
        $this->attributeCombinationId = $attributeCombinationId;
        $this->attribute = $attribute;
        $this->stock = $stock;
        $this->formattedPrice = $formattedPrice;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->priceTaxIncluded = $priceTaxIncluded;
        $this->location = $location;
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getAttributeCombinationId(): int
    {
        return $this->attributeCombinationId;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcluded(): float
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return float
     */
    public function getPriceTaxIncluded(): float
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $name
     */
    public function appendAttributeName(string $name)
    {
        $this->attribute .= ' - ' . $name;
    }
}
