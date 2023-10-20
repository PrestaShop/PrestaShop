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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        } catch (PrestaShopException $e) {
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
        } catch (PrestaShopException $e) {
            throw new AttributeGroupException(sprintf('An error occurred when trying to delete attribute with id %s', $attributeGroup->id));
        }
    }
}
