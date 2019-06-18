<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup;

use AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShopException;

/**
 * Provides data required for attribute group view action
 */
final class AttributeGroupViewDataProvider
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct($contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * @param int $attributeGroupId
     *
     * @return bool
     *
     * @throws AttributeGroupException
     * @throws AttributeGroupNotFoundException
     */
    public function isColorGroup($attributeGroupId)
    {
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        return $attributeGroup->is_color_group;
    }

    /**
     * Provides the name of attribute group by its id
     *
     * @param int $attributeGroupId
     *
     * @return string
     *
     * @throws AttributeGroupException
     * @throws AttributeGroupNotFoundException
     */
    public function getAttributeGroupNameById($attributeGroupId)
    {
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        return $attributeGroup->public_name[$this->contextLangId];
    }

    /**
     * Gets legacy AttributeGroup object by provided id
     *
     * @param int $attributeGroupId
     *
     * @return AttributeGroup
     *
     * @throws AttributeGroupException
     * @throws AttributeGroupNotFoundException
     */
    private function getAttributeGroupById($attributeGroupId)
    {
        try {
            $attributeGroup = new AttributeGroup($attributeGroupId);

            if ($attributeGroup->id !== $attributeGroupId) {
                throw new AttributeGroupNotFoundException(
                    sprintf('Attribute group with id "%s" was not found.', $attributeGroupId)
                );
            }
        } catch (PrestaShopException $e) {
            throw new AttributeGroupException(
                sprintf('An error occurred when trying to get attribute group with id %s', $attributeGroupId)
            );
        }

        return $attributeGroup;
    }
}
