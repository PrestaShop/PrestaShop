/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EntitySearchInput from '@components/entity-search-input';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import addressFormMap from './address-form-map';

const {$} = window;

$(() => {
  const $customerSearch = $(addressFormMap.customerSearchContainer);

  if ($customerSearch.length) {
    const $searchInput = $customerSearch.find('input.entity-search-input');
    const $selectedCustomers = $customerSearch.find('.entities-list');
    const $formWrapper = $customerSearch.closest('.form-wrapper');
    const guestLabel = $formWrapper.attr('data-guest-label') || 'Guest';
    const customerLabel = $formWrapper.attr('data-customer-label') || 'Customer';

    // The search input should only be required when no customer is selected.
    const updateCustomerRequired = (): void => {
      const hasSelectedCustomer = $selectedCustomers.find('.entity-item').length > 0;
      $searchInput.prop('required', !hasSelectedCustomer);
    };

    new EntitySearchInput($customerSearch, {
      // The existing customer-search endpoint may return the legacy envelope.
      responseTransformer: (response: any) => Object.values(response.customers ?? response),
      suggestionTemplate: (customer: any) => {
        const text = document.createElement('div');
        const accountType = customer.is_guest ? guestLabel : customerLabel;

        text.textContent = `#${customer.id_customer} - ${customer.fullname_and_email} (${accountType})`;

        return text.outerHTML;
      },
      onRemovedContent: () => {
        updateCustomerRequired();
      },
      onSelectedContent: (_item: JQuery, customer: any) => {
        $(addressFormMap.addressFirstnameInput).val(customer.firstname);
        $(addressFormMap.addressLastnameInput).val(customer.lastname);
        $(addressFormMap.addressCompanyInput).val(customer.company || '');
        updateCustomerRequired();
      },
    });

    // Observe the actual selection list so the state stays correct regardless of
    // whether EntitySearchInput calls its callbacks before or after DOM updates.
    if ($selectedCustomers.length) {
      new MutationObserver(updateCustomerRequired).observe($selectedCustomers[0], {
        childList: true,
        subtree: true,
      });
    }
    updateCustomerRequired();
  }
  new window.prestashop.component.CountryStateSelectionToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressStateSelect,
    addressFormMap.addressStateBlock,
  );
  new window.prestashop.component.CountryDniRequiredToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressDniInput,
    addressFormMap.addressDniInputLabel,
  );
  new CountryPostcodeRequiredToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressPostcodeInput,
    addressFormMap.addressPostcodeInputLabel,
  );
});
