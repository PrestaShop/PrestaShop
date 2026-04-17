<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Combines value objects into a valid structure for generating combinations
 */
class GroupedAttributeIds
{
    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @var AttributeId[]
     */
    private $attributeIds = [];

    /**
     * @param int $attributeGroupId
     * @param array $attributeIds
     *
     * @throws AttributeConstraintException
     * @throws AttributeGroupConstraintException
     */
    public function __construct(
        int $attributeGroupId,
        array $attributeIds
    ) {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
        $this->setAttributeIds($attributeIds);
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }

    /**
     * @return AttributeId[]
     */
    public function getAttributeIds(): array
    {
        return $this->attributeIds;
    }

    /**
     * @param int[] $attributeIds
     *
     * @throws AttributeConstraintException
     */
    private function setAttributeIds(array $attributeIds): void
    {
        foreach ($attributeIds as $attributeId) {
            $this->attributeIds[] = new AttributeId($attributeId);
        }
    }
}
