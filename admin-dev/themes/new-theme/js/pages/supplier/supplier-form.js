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

import CountryStateSelectionToggler from '@components/country-state-selection-toggler';
import CountryDniRequiredToggler from '@components/country-dni-required-toggler';
import SupplierMap from './supplier-map';
import TranslatableInput from '@components/translatable-input';
import TranslatableField from '@components/translatable-field';
import TaggableField from '@components/taggable-field';
import ChoiceTree from '@components/form/choice-tree';
import TinyMCEEditor from '@components/tinymce-editor';

const $ = window.$;

$(document).ready(() => {
  const shopChoiceTree = new ChoiceTree('#supplier_shop_association');
  shopChoiceTree.enableAutoCheckChildren();

  new CountryStateSelectionToggler(
    SupplierMap.supplierCountrySelect,
    SupplierMap.supplierStateSelect,
    SupplierMap.supplierStateBlock
  );

  new CountryDniRequiredToggler(
    SupplierMap.supplierCountrySelect,
    SupplierMap.supplierDniInput,
    SupplierMap.supplierDniInputLabel
  );

  new TinyMCEEditor();
  new TranslatableInput();
  new TranslatableField();
  new TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true
    }
  });
});
