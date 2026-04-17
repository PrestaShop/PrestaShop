/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import AttributeFormMap from '@pages/attribute/form/attribute-form-map';
import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'TranslatableField',
    ],
  );

  new window.prestashop.component.ChoiceTree(AttributeFormMap.attributeShopAssociationInput).enableAutoCheckChildren();

  new FormSubmitButton();
});

document.addEventListener('DOMContentLoaded', () => {
  const attributeGroupSelect = document.querySelector(AttributeFormMap.attributeGroupSelect) as HTMLSelectElement | null;
  const attributeColorRow = document.querySelector(AttributeFormMap.attributeColorFormRow) as HTMLElement | null;
  const attributeTextureRow = document.querySelector(AttributeFormMap.attributeTextureFormRow) as HTMLElement | null;

  if (!attributeGroupSelect || !attributeColorRow || !attributeTextureRow) return;

  const toggleDisplay = () => {
    const selectedOption = attributeGroupSelect?.selectedOptions[0];
    const isColorGroup = selectedOption?.dataset.iscolorgroup;
    const displayValue = isColorGroup ? 'flex' : 'none';

    attributeColorRow.style.display = displayValue;
    attributeTextureRow.style.display = displayValue;
  };

  toggleDisplay();

  attributeGroupSelect?.addEventListener('change', toggleDisplay);
});
