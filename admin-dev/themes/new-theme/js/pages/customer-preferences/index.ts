/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CustomerPreferencesMap from '@pages/customer-preferences/customer-preferences-map';

$(() => {
  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
    ],
  );

  // Required fields : Display alert for optin checkbox
  $(CustomerPreferencesMap.switchPartnerOffers).on('click', () => handleFormCheckboxPartnerOffers());

  function handleFormCheckboxPartnerOffers(): void {
    $(CustomerPreferencesMap.checkboxPartnerOffersAlertOptin).toggleClass(
      'd-none',
      ($(CustomerPreferencesMap.checkboxPartnerOffers).val() === '1'),
    );
  }
});
