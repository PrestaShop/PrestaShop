/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Defines all selectors that are used in customer add/edit form.
 */
export default {
  passwordInput: '#customer_password',
  passwordStrengthFeedbackContainer: '.password-strength-feedback',
  requiredFieldsFormAlertOptin: '#customerRequiredFieldsAlertMessageOptin',
  requiredFieldsFormCheckboxOptin: '#customerRequiredFieldsContainer input[type="checkbox"][value="optin"]',

  // Customer group inputs
  customerGroupCheckboxes: 'input[type="checkbox"][name="customer[group_ids][]"]',
  defaultGroupSelect: '#customer_default_group_id',
  defaultGroupSelectedOption: '#customer_default_group_id option:selected',

  // Is guest switch selector
  isGuestRadios: 'input[name="customer[is_guest]"]',

  // Is enabled switch and it's radios
  isEnabledRadios: 'input[name="customer[is_enabled]"]',
  isEnabledRadiosOn: 'input[name="customer[is_enabled]"][value="1"]',
  isEnabledRadiosOff: 'input[name="customer[is_enabled]"][value="0"]',
};
