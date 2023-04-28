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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attribute\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attribute\QueryHandler\GetAttributeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attribute\QueryResult\EditableAttribute;
use PrestaShop\PrestaShop\Core\Domain\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShopException;
use ProductAttribute;

/**
 * Handles query which gets attribute group for editing
 */
final class GetAttributeForEditingHandler implements GetAttributeForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetAttributeForEditing $query): EditableAttribute
    {
        $attributeId = $query->getAttributeId();
        $attribute = $this->getAttribute($attributeId);

        return new EditableAttribute(
            $attributeId,
            new AttributeGroupId($attribute->id_attribute_group),
            $attribute->name,
            $attribute->color,
            $attribute->getAssociatedShops()
        );
    }

    /**
     * Gets legacy Attribute group
     *
     * @param AttributeId $attributeId
     *
     * @return ProductAttribute
     *
     * @throws AttributeException
     * @throws AttributeNotFoundException
     */
    protected function getAttribute(AttributeId $attributeId): ProductAttribute
    {
        try {
            $attribute = new ProductAttribute($attributeId->getValue());
        } catch (PrestaShopException $e) {
            throw new AttributeException('Failed to create new attribute', 0, $e);
        }

        if ($attribute->id !== $attributeId->getValue()) {
            throw new AttributeNotFoundException(sprintf('Attribute with id "%s" was not found.', $attributeId->getValue()));
        }

        return $attribute;
    }
}
