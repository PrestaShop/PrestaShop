<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShopException;
use ProductAttribute;

/**
 * Provides common methods for attribute command/query handlers
 */
abstract class AbstractAttributeHandler
{
    /**
     * @param AttributeId $attributeId
     *
     * @return ProductAttribute
     *
     * @throws AttributeException
     */
    protected function getAttributeById($attributeId)
    {
        $idValue = $attributeId->getValue();

        try {
            $attribute = new ProductAttribute($idValue);

            if ($attribute->id !== $idValue) {
                throw new AttributeNotFoundException(sprintf('Attribute with id "%s" was not found.', $idValue));
            }
        } catch (PrestaShopException) {
            throw new AttributeException(sprintf('An error occurred when trying to get attribute with id %s', $idValue));
        }

        return $attribute;
    }

    /**
     * @param ProductAttribute $attribute
     *
     * @return bool
     *
     * @throws AttributeException
     */
    protected function deleteAttribute(ProductAttribute $attribute)
    {
        try {
            return $attribute->delete();
        } catch (PrestaShopException) {
            throw new AttributeException(sprintf('An error occurred when trying to delete attribute with id %s', $attribute->id));
        }
    }
}
