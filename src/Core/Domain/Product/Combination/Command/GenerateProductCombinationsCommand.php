<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
