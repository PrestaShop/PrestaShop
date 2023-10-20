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
import {Attribute, AttributeGroup} from '@pages/product/combination/types';

export default {
  methods: {
    /**
     * The selected attribute is provided as a parameter instead od using this reference because it helps the
     * observer work better whe this.selectedAttributeGroups is explicitly used as an argument.
     *
     * @param {Object} attribute
     * @param {Object} attributeGroup
     * @param {Object} attributeGroups
     *
     * @returns {boolean}
     */
    isSelected(attribute: Attribute, attributeGroup: AttributeGroup, attributeGroups: Record<string, AttributeGroup>): boolean {
      if (!Object.prototype.hasOwnProperty.call(attributeGroups, attributeGroup.id)) {
        return false;
      }

      return attributeGroups[attributeGroup.id].attributes.includes(attribute);
    },
  },
};
