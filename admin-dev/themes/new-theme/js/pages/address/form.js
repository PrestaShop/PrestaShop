/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import AutocompleteWithEmail from '@components/form/autocomplete-with-email';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';
import CountryDniRequiredToggler from '@components/country-dni-required-toggler';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import addressFormMap from './address-form-map';
import FieldVisibilityForCountryToggler from './field-visibility-for-country-toggler';

const {$} = window;

$(() => {
  new AutocompleteWithEmail(
    addressFormMap.addressEmailInput,
    {
      firstName: addressFormMap.addressFirstnameInput,
      lastName: addressFormMap.addressLastnameInput,
      company: addressFormMap.addressCompanyInput,
    },
  );
  new CountryStateSelectionToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressStateSelect,
    addressFormMap.addressStateBlock,
  );
  new CountryDniRequiredToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressDniInput,
    addressFormMap.addressDniInputLabel,
  );
  new CountryPostcodeRequiredToggler(
    addressFormMap.addressCountrySelect,
    addressFormMap.addressPostcodeInput,
    addressFormMap.addressPostcodeInputLabel,
  );

  //@todo: still have to add all fields. Handle required *
  new FieldVisibilityForCountryToggler(addressFormMap.addressCountrySelect, {
    alias: addressFormMap.aliasFieldRow,
    dni: addressFormMap.dniFieldRow,
    company: addressFormMap.companyFieldRow,
    vat_number: addressFormMap.vatNumberFieldRow,
    address2: addressFormMap.address2FieldRow,
    postcode: addressFormMap.postCodeFieldRow,
    phone: addressFormMap.phoneFieldRow,
    phone_mobile: addressFormMap.phoneMobileFieldRow,
    otherFieldRow: addressFormMap.otherFieldRow,
  });
});
