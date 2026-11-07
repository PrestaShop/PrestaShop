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
    const guestLabel = $customerSearch.attr('data-guest-badge-label') || 'Guest';

    // The search input should only be required when no customer is selected.
    const updateCustomerRequired = (): void => {
      const hasSelectedCustomer = $selectedCustomers.find('.entity-item').length > 0;
      $searchInput.prop('required', !hasSelectedCustomer);
    };

    const lastPrefill: Record<string, string> = {};

    const prefillField = (selector: string, value: string | null): void => {
      const $field = $(selector);
      const currentValue = String($field.val() ?? '');

      if (currentValue !== '' && currentValue !== lastPrefill[selector]) {
        return;
      }

      const newValue = value ?? '';
      $field.val(newValue);
      lastPrefill[selector] = newValue;
    };

    new EntitySearchInput($customerSearch, {
      // The existing customer-search endpoint may return the legacy envelope.
      responseTransformer: (response: any) => Object.values(response.customers ?? response),
      suggestionTemplate: (customer: any) => {
        const text = document.createElement('div');
        const guestSuffix = customer.is_guest ? ` (${guestLabel})` : '';

        text.textContent = `#${customer.id_customer} - ${customer.fullname_and_email}${guestSuffix}`;

        return text.outerHTML;
      },
      onRemovedContent: () => {
        updateCustomerRequired();
      },
      onSelectedContent: (_item: JQuery, customer: any) => {
        prefillField(addressFormMap.addressFirstnameInput, customer.firstname);
        prefillField(addressFormMap.addressLastnameInput, customer.lastname);
        prefillField(addressFormMap.addressCompanyInput, customer.company);
        updateCustomerRequired();
      },
    });

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
