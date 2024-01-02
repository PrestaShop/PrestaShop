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
