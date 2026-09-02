<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Retrieves attribute group data for editing
 */
class GetAttributeGroupForEditing
{
    /**
     * @var AttributeGroupId
     */
    private $attributeGroupId;

    /**
     * @param int $attributeGroupId
     */
    public function __construct(int $attributeGroupId)
    {
        $this->attributeGroupId = new AttributeGroupId($attributeGroupId);
    }

    /**
     * @return AttributeGroupId
     */
    public function getAttributeGroupId(): AttributeGroupId
    {
        return $this->attributeGroupId;
    }
}
