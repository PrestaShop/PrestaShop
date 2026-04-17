<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Deletes attribute group by provided id
 */
final class DeleteAttributeGroupCommand
{
    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @param int $attributeGroupId
     *
     * @throws AttributeGroupConstraintException
     */
    public function __construct($attributeGroupId)
    {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId()
    {
        return $this->attributeGroupId;
    }
}
