/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';
import SupplierMap from './supplier-map';

const {$} = window;

$(() => {
  new window.prestashop.component.ChoiceTree('#supplier_shop_association').enableAutoCheckChildren();
  new window.prestashop.component.CountryStateSelectionToggler(
    SupplierMap.supplierCountrySelect,
    SupplierMap.supplierStateSelect,
    SupplierMap.supplierStateBlock,
  );
  new window.prestashop.component.CountryDniRequiredToggler(
    SupplierMap.supplierCountrySelect,
    SupplierMap.supplierDniInput,
    SupplierMap.supplierDniInputLabel,
  );

  window.prestashop.component.initComponents(
    [
      'TinyMCEEditor',
      'TranslatableInput',
      'TranslatableField',
    ],
  );

  new window.prestashop.component.TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  new FormSubmitButton();
});
