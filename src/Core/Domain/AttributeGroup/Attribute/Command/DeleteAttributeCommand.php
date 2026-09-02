<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;

/**
 * Deletes Attribute by provided id
 */
final class DeleteAttributeCommand
{
    /**
     * @var AttributeId
     */
    private $attributeId;

    /**
     * @param int $attributeId
     *
     * @throws AttributeConstraintException
     */
    public function __construct($attributeId)
    {
        $this->attributeId = new AttributeId($attributeId);
    }

    /**
     * @return AttributeId
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }
}
