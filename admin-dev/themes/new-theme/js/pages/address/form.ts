/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import AutocompleteWithEmail from '@components/form/autocomplete-with-email';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import addressFormMap from './address-form-map';

const {$} = window;

$(() => {
  new AutocompleteWithEmail(addressFormMap.addressEmailInput, {
    firstName: addressFormMap.addressFirstnameInput,
    lastName: addressFormMap.addressLastnameInput,
    company: addressFormMap.addressCompanyInput,
  });
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
