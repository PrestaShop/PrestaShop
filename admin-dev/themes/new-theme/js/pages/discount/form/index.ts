/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormFieldToggler, {ToggleType, SwitchEventData} from '@components/form/form-field-toggler';
import DiscountMap from '@pages/discount/discount-map';
import CreateFreeGiftDiscount from '@pages/discount/form/create-free-gift-discount';
import SpecificProducts from '@pages/discount/form/specific-products';
import initGroupedItemCollection from '@PSVue/components/grouped-item-collection';
import {getAllAttributeGroups, getAllFeatureGroups} from '@pages/discount/form/services';
import CustomerSearchInput from '@components/form/customer-search-input';

$(() => {
  // Scroll to the first field with a validation error
  const firstError = document.querySelector<HTMLElement>('form .alert-danger');

  if (firstError) {
    firstError.scrollIntoView({behavior: 'smooth', block: 'center'});
  }

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'ToggleChildrenChoice',
      'GeneratableInput',
      'ChoiceTree',
      'ChoiceTable',
      'EventEmitter',
      'DisablingSwitch',
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

  const {eventEmitter} = window.prestashop.instance;

  if (document.querySelector(DiscountMap.reductionTypeSelect)) {
    // Listen for the reduction type toggle to swap currency selector / % symbol
    eventEmitter.on('discountReductionTypeChanged', (data: SwitchEventData) => {
      const currencySelect = document.querySelector<HTMLSelectElement>(DiscountMap.reductionCurrencySelect);
      const inputGroupAppend = document.querySelector<HTMLElement>(DiscountMap.reductionCurrencyAppend);

      if (!currencySelect || !inputGroupAppend) {
        return;
      }

      let percentSpan = inputGroupAppend.querySelector<HTMLSpanElement>(DiscountMap.reductionPercentSpan);

      if (data.disable) {
        // Switched to percentage: hide currency select, show % symbol
        currencySelect.classList.add('d-none');

        if (!percentSpan) {
          percentSpan = document.createElement('span');
          percentSpan.className = 'input-group-text currency-money-percent';
          percentSpan.textContent = '%';
          inputGroupAppend.appendChild(percentSpan);
        }

        percentSpan.classList.remove('d-none');
      } else {
        // Switched to amount: show currency select, hide % symbol
        currencySelect.classList.remove('d-none');

        if (percentSpan) {
          percentSpan.classList.add('d-none');
        }
      }
    });

    // Toggle include_tax visibility and emit event for currency/% swap
    new FormFieldToggler({
      disablingInputSelector: DiscountMap.reductionTypeSelect,
      matchingValue: 'percentage',
      targetSelector: DiscountMap.reductionIncludeTaxRow,
      disableOnMatch: true,
      toggleType: ToggleType.visibility,
      switchEvent: 'discountReductionTypeChanged',
    });
  }

  eventEmitter.on('ToggleChildrenChoice:toggled', (radio: HTMLInputElement) => {
    // We need to trigger change those select2 elements because the component is not loaded when the page is displayed
    // if we don't trigger change them, the placeholder cannot be loaded correctly.
    if (radio.value === 'country') {
      $(DiscountMap.countriesSelect).trigger('change');
    }
    if (radio.value === 'carriers') {
      $(DiscountMap.carriersSelect).trigger('change');
    }
    if (radio.value === 'single_customer') {
      $(DiscountMap.quantityPerCustomerInput).parents('.form-group').hide();
    }
    if (radio.value === 'customer_groups' || radio.value === 'all_customers') {
      $(DiscountMap.quantityPerCustomerInput).parents('.form-group').show();
    }
  });

  if ($(DiscountMap.customerEligibilityInput).find('input[type="radio"]:checked').attr('value') === 'single_customer') {
    $(DiscountMap.quantityPerCustomerInput).parents('.form-group').hide();
  } else {
    $(DiscountMap.quantityPerCustomerInput).parents('.form-group').show();
  }

  new FormFieldToggler({
    disablingInputSelector: DiscountMap.periodNeverExpiresCheckbox,
    matchingValue: '1',
    disableOnMatch: true,
    targetSelector: DiscountMap.periodExpiryDateFormGroup,
    toggleType: ToggleType.visibility,
  });

  new FormFieldToggler({
    disablingInputSelector: DiscountMap.periodNeverExpiresCheckbox,
    matchingValue: '1',
    disableOnMatch: true,
    targetSelector: DiscountMap.periodExpiryDateFormGroup,
    toggleType: ToggleType.availability,
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

  new window.prestashop.component.ChoiceTree(DiscountMap.categoryTree);

  initGroupedItemCollection(DiscountMap.productSegmentAttributes, getAllAttributeGroups);
  initGroupedItemCollection(DiscountMap.productSegmentFeatures, getAllFeatureGroups);
});
