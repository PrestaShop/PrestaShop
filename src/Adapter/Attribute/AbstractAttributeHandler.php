<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute;

use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\ValueObject\AttributeId;
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
        } catch (PrestaShopException $e) {
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
        } catch (PrestaShopException $e) {
            throw new AttributeException(sprintf('An error occurred when trying to delete attribute with id %s', $attribute->id));
        }
    }
}
