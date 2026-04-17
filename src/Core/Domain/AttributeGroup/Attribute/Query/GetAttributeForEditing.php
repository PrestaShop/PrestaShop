<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;

/**
 * Retrieves attribute group data for editing
 */
class GetAttributeForEditing
{
    /**
     * @var AttributeId
     */
    private $attributeId;

    /**
     * @param int $attributeId
     */
    public function __construct(int $attributeId)
    {
        $this->attributeId = new AttributeId($attributeId);
    }

    /**
     * @return AttributeId
     */
    public function getAttributeId(): AttributeId
    {
        return $this->attributeId;
    }
}
