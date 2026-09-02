/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
