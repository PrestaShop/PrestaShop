/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    updateCustomerDisabledBadges(DiscountMap.customerSearchContainer);
    observeCustomerListForDisabledBadges(DiscountMap.customerSearchContainer);
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
    if (radio.value === 'single_customer') {
      $(DiscountMap.quantityPerCustomerInput).parents('.form-group').hide();
    }
    if (radio.value === 'customer_groups' || radio.value === 'all_customers') {
      $(DiscountMap.quantityPerCustomerInput).parents('.form-group').show();
    }
    const customerEligibilityToggle = radio.closest(DiscountMap.customerEligibilityInput);
    if (customerEligibilityToggle) {
      const card = customerEligibilityToggle.closest('.card');
      const messageEl = card?.querySelector(DiscountMap.groupFeatureDisabledMessage);
      if (messageEl) {
        if (radio.value === 'customer_groups') {
          messageEl.classList.remove('d-none');
        } else {
          messageEl.classList.add('d-none');
        }
      }
    }
  });

  if ($(DiscountMap.customerEligibilityInput).find('input[type="radio"]:checked').attr('value') === 'single_customer') {
    $(DiscountMap.quantityPerCustomerInput).parents('.form-group').hide();
  } else {
    $(DiscountMap.quantityPerCustomerInput).parents('.form-group').show();
  }

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

function updateCustomerDisabledBadges(container: string): void {
  $(container).find('.entity-item').each(function () {
    const $item = $(this);
    const activeInput = $item.find('input[name*="[active]"]')[0] as HTMLInputElement | undefined;
    const $badge = $item.find(DiscountMap.customerDisabledBadge);

    if (activeInput && $badge.length) {
      const value = activeInput.value;
      if (value === '0') {
        $badge.removeClass('d-none');
      } else {
        $badge.addClass('d-none');
      }
    }
  });
}

function observeCustomerListForDisabledBadges(container: string): void {
  const listEl = document.querySelector(`${container} .entities-list`);
  if (!listEl) {
    return;
  }

  const observer = new MutationObserver(() => {
    setTimeout(() => updateCustomerDisabledBadges(container), 0);
  });

  observer.observe(listEl, {childList: true, subtree: true});
}
