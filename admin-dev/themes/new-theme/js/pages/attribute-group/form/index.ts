/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import AttributeGroupFormMap from '@pages/attribute-group/form/attribute-group-form-map';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'TranslatableField',
    ],
  );

  new window.prestashop.component.ChoiceTree(AttributeGroupFormMap.attributeGroupShopAssociationInput).enableAutoCheckChildren();
});
