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

import AutocompleteWithEmail from '@components/form/autocomplete-with-email';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';
import CountryDniRequiredToggler from '@components/country-dni-required-toggler';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import addressFormMap from './address-form-map';

const {$} = window;

$(document).ready(() => {
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
});
