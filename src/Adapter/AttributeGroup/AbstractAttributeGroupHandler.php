<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup;

use AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShopException;

/**
 * Provides reusable methods for attribute group handlers
 */
abstract class AbstractAttributeGroupHandler
{
    /**
     * @param AttributeGroupId $attributeGroupId
     *
     * @return AttributeGroup
     *
     * @throws AttributeGroupException
     */
    protected function getAttributeGroupById($attributeGroupId)
    {
        $idValue = $attributeGroupId->getValue();

        try {
            $attributeGroup = new AttributeGroup($idValue);

            if ($attributeGroup->id !== $idValue) {
                throw new AttributeGroupNotFoundException(sprintf('Attribute group with id "%s" was not found.', $idValue));
            }
        } catch (PrestaShopException) {
            throw new AttributeGroupException(sprintf('An error occurred when trying to get attribute group with id %s', $idValue));
        }

        return $attributeGroup;
    }

    /**
     * @param AttributeGroup $attributeGroup
     *
     * @return bool
     *
     * @throws AttributeGroupException
     */
    protected function deleteAttributeGroup(AttributeGroup $attributeGroup)
    {
        try {
            return $attributeGroup->delete();
        } catch (PrestaShopException) {
            throw new AttributeGroupException(sprintf('An error occurred when trying to delete attribute with id %s', $attributeGroup->id));
        }
    }
}
