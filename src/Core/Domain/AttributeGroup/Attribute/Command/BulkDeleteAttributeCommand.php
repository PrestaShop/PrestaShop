<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;

/**
 * Deletes attributes in bulk action
 */
final class BulkDeleteAttributeCommand
{
    /**
     * @var AttributeId[]
     */
    private $attributeIds;

    /**
     * @param int[] $attributeIds
     *
     * @throws AttributeConstraintException
     */
    public function __construct(array $attributeIds)
    {
        $this->setAttributeIds($attributeIds);
    }

    /**
     * @return AttributeId[]
     */
    public function getAttributeIds()
    {
        return $this->attributeIds;
    }

    /**
     * @param array $attributeIds
     *
     * @throws AttributeConstraintException
     */
    private function setAttributeIds(array $attributeIds)
    {
        foreach ($attributeIds as $attributeId) {
            $this->attributeIds[] = new AttributeId($attributeId);
        }
    }
}
