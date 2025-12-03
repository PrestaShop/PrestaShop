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

import PriceReductionManager from '@components/form/price-reduction-manager';
import DiscountMap from '@pages/discount/discount-map';
import CreateFreeGiftDiscount from '@pages/discount/form/create-free-gift-discount';
import SpecificProducts from '@pages/discount/form/specific-products';
import initGroupedItemCollection from '@PSVue/components/grouped-item-collection';
import {getAllAttributeGroups, getAllFeatureGroups} from '@pages/discount/form/services';
import CustomerSearchInput from '@components/form/customer-search-input';

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'ToggleChildrenChoice',
      'GeneratableInput',
      'ChoiceTree',
      'ChoiceTable',
      'EventEmitter',
    ],
  );

  new CreateFreeGiftDiscount();
  new SpecificProducts();

  // Initialize customer search for single customer eligibility
  if ($(DiscountMap.customerSearchContainer).length > 0) {
    new CustomerSearchInput(
      DiscountMap.customerSearchContainer,
      '.js-customer-item',
      () => null,
    );
  }

  const reductionTypeSelect = document.querySelector(DiscountMap.reductionTypeSelect);

  if (reductionTypeSelect) {
    reductionTypeSelect.addEventListener('change', toggleCurrency);
    new PriceReductionManager(
      DiscountMap.reductionTypeSelect,
      DiscountMap.includeTaxInput,
      DiscountMap.currencySelect,
      DiscountMap.reductionValueSymbol,
      DiscountMap.currencySelectContainer,
    );
    toggleCurrency();
  }

  const {eventEmitter} = window.prestashop.instance;

  eventEmitter.on('ToggleChildrenChoice:toggled', (radio: HTMLInputElement) => {
    // We need to trigger change those select2 elements because the component is not loaded when the page is displayed
    // if we don't trigger change them, the placeholder cannot be loaded correctly.
    if (radio.value === 'country') {
      $(DiscountMap.countriesSelect).trigger('change');
    }
    if (radio.value === 'carriers') {
      $(DiscountMap.carriersSelect).trigger('change');
    }
  });

  $(DiscountMap.countriesSelect).select2({
    templateResult: formatOption,
    templateSelection: formatOption,
    theme: 'bootstrap4',
  });

  $(DiscountMap.carriersSelect).select2({
    templateResult: formatOption,
    templateSelection: formatOption,
    theme: 'bootstrap4',
  });

  function formatOption(option: any) {
    if (!option.element || !option.element.dataset.logo) {
      return option.text;
    }
    const imageUrl = option.element.dataset.logo;

    return $(
      `<span><img src="${imageUrl}"/> ${option.text} </span>`,
    );
  }

  function toggleCurrency(): void {
    if ($(DiscountMap.reductionTypeSelect).val() === 'percentage') {
      $(DiscountMap.currencySelect).fadeOut();
    } else {
      $(DiscountMap.currencySelect).fadeIn();
    }
  }

  new window.prestashop.component.ChoiceTree(DiscountMap.categoryTree);

  initGroupedItemCollection(DiscountMap.productSegmentAttributes, getAllAttributeGroups);
  initGroupedItemCollection(DiscountMap.productSegmentFeatures, getAllFeatureGroups);
});
