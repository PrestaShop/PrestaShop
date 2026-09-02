/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ManufacturerAddressMap from '@pages/manufacturer/manufacturer-address-map';

const {$} = window;

$(() => {
  new window.prestashop.component.CountryStateSelectionToggler(
    ManufacturerAddressMap.manufacturerAddressCountrySelect,
    ManufacturerAddressMap.manufacturerAddressStateSelect,
    ManufacturerAddressMap.manufacturerAddressStateBlock,
  );
  new window.prestashop.component.CountryDniRequiredToggler(
    ManufacturerAddressMap.manufacturerAddressCountrySelect,
    ManufacturerAddressMap.manufacturerAddressDniInput,
    ManufacturerAddressMap.manufacturerAddressDniInputLabel,
  );
});
