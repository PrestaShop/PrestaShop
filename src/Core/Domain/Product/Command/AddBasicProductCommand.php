<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

class AddBasicProductCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var ProductType
     */
    private $type;

    /**
     * @var Number
     */
    private $price;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var CategoryId[]
     */
    private $categoryIds;

    /**
     * @var string[]|null
     */
    private $localizedShortDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * @var ManufacturerId|null
     */
    private $manufacturerId;

    /**
     * @var TaxRulesGroupId|null
     */
    private $taxRulesGroupId;

    /**
     * @param array $localizedNames
     * @param int $type
     * @param string $price
     * @param int $quantity
     * @param array $categoryIds
     */
    public function __construct(
        array $localizedNames,
        int $type,
        string $price,
        int $quantity,
        array $categoryIds
    ) {
        //@todo: validate fields
        $this->localizedNames = $localizedNames;
        $this->type = new ProductType($type);
        $this->price = new Number($price);
        $this->quantity = $quantity;
        $this->setCategoryIds($categoryIds);
    }

    /**
     * @param string[] $localizedShortDescriptions
     *
     * @return AddBasicProductCommand
     */
    public function setLocalizedShortDescriptions(array $localizedShortDescriptions): AddBasicProductCommand
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;

        return $this;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return AddBasicProductCommand
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): AddBasicProductCommand
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @param int $manufacturerId
     *
     * @return AddBasicProductCommand
     */
    public function setManufacturerId(int $manufacturerId): AddBasicProductCommand
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);

        return $this;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return ProductType
     */
    public function getType(): ProductType
    {
        return $this->type;
    }

    /**
     * @return Number
     */
    public function getPrice(): Number
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return CategoryId[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    /**
     * @return string[]
     */
    public function getLocalizedShortDescriptions(): array
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return ManufacturerId|null
     */
    public function getManufacturerId(): ?ManufacturerId
    {
        return $this->manufacturerId;
    }

    /**
     * @return TaxRulesGroupId|null
     */
    public function getTaxRulesGroupId(): ?TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @param int $taxRulesGroupId
     *
     * @return AddBasicProductCommand
     */
    public function setTaxRulesGroupId(int $taxRulesGroupId): AddBasicProductCommand
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);

        return $this;
    }

    /**
     * @param int[] $categoryIds
     */
    private function setCategoryIds(array $categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $this->categoryIds[] = new CategoryId($categoryId);
        }
    }
}
