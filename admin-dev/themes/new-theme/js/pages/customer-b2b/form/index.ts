/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import CustomerB2bFormMap from '@pages/customer-b2b/form/customer-b2b-form-map';
import CustomerSearchInput from '@components/form/customer-search-input';
import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';

$(() => {
  if (!document.querySelector(CustomerB2bFormMap.useExistingCustomerChoice)) {
    return;
  }

  const sections = [
    {targetSelector: CustomerB2bFormMap.newCustomerSection, disableOnMatch: true},
    {targetSelector: CustomerB2bFormMap.existingCustomerSection, disableOnMatch: false},
  ];

  sections.forEach(({targetSelector, disableOnMatch}) => {
    [ToggleType.visibility, ToggleType.availability].forEach((toggleType) => {
      new FormFieldToggler({
        disablingInputSelector: CustomerB2bFormMap.useExistingCustomerChoice,
        matchingValue: '1',
        targetSelector,
        disableOnMatch,
        toggleType,
      });
    });
  });

  new CustomerSearchInput(CustomerB2bFormMap.existingCustomerSearchContainer, '.js-customer-item', () => null);
});
