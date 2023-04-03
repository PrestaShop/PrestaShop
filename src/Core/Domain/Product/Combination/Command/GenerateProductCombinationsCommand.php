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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\GroupedAttributeIds;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Generates attribute combinations for product
 */
class GenerateProductCombinationsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var GroupedAttributeIds[]
     */
    private $groupedAttributeIdsList;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param array<int, array<int>> $groupedAttributeIds key-value pairs where key is the attribute group id and value is the list of that group attribute ids
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(
        int $productId,
        array $groupedAttributeIds,
        ShopConstraint $shopConstraint
    ) {
        $this->setGroupedAttributeIdsList($groupedAttributeIds);
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getGroupedAttributeIdsList(): array
    {
        return $this->groupedAttributeIdsList;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param array $groupedAttributeIds
     *
     * @throws AttributeConstraintException
     * @throws AttributeGroupConstraintException
     */
    private function setGroupedAttributeIdsList(array $groupedAttributeIds): void
    {
        $groupedAttributeIdsList = [];

        foreach ($groupedAttributeIds as $groupId => $attributeIdValues) {
            $groupedAttributeIdsList[] = new GroupedAttributeIds($groupId, $attributeIdValues);
        }

        $this->groupedAttributeIdsList = $groupedAttributeIdsList;
    }
}
