<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Deletes attribute groups in bulk action by provided ids
 */
final class BulkDeleteAttributeGroupCommand
{
    /**
     * @var AttributeGroupId[]
     */
    private $attributeGroupIds;

    /**
     * @param int[] $attributeGroupIds
     *
     * @throws AttributeGroupConstraintException
     */
    public function __construct(array $attributeGroupIds)
    {
        $this->setAttributeGroupIds($attributeGroupIds);
    }

    /**
     * @return AttributeGroupId[]
     */
    public function getAttributeGroupIds()
    {
        return $this->attributeGroupIds;
    }

    /**
     * @param array $attributeGroupIds
     *
     * @throws AttributeGroupConstraintException
     */
    private function setAttributeGroupIds(array $attributeGroupIds)
    {
        foreach ($attributeGroupIds as $attributeGroupId) {
            $this->attributeGroupIds[] = new AttributeGroupId($attributeGroupId);
        }
    }
}
