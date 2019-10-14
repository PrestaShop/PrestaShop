<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class ProductForOrderCreation
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var ProductForOrderCreationCombinations|null
     */
    private $combinations;

    /**
     * @var ProductCustomizationFields|null
     */
    private $customizationFields;

    /**
     * @param int $productId
     * @param string $name
     * @param string $formattedPrice
     * @param int $stock
     */
    public function __construct(
        int $productId,
        string $name,
        string $formattedPrice,
        int $stock
    ) {
        $this->productId = $productId;
        $this->name = $name;
        $this->formattedPrice = $formattedPrice;
        $this->stock = $stock;
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
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return ProductForOrderCreationCombinations|null
     */
    public function getCombinations(): ?ProductForOrderCreationCombinations
    {
        return $this->combinations;
    }

    /**
     * @param ProductForOrderCreationCombinations $combinations
     *
     * @return ProductForOrderCreation
     */
    public function setCombinations(ProductForOrderCreationCombinations $combinations): ProductForOrderCreation
    {
        $this->combinations = $combinations;

        return $this;
    }

    /**
     * @return ProductCustomizationFields|null
     */
    public function getCustomizationFields(): ?ProductCustomizationFields
    {
        return $this->customizationFields;
    }

    /**
     * @param ProductCustomizationFields $customizationFields
     *
     * @return ProductForOrderCreation
     */
    public function setCustomizationFields(ProductCustomizationFields $customizationFields): ProductForOrderCreation
    {
        $this->customizationFields = $customizationFields;

        return $this;
    }
}
