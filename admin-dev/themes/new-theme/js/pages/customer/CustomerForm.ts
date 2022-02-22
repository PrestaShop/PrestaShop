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

import customerFormMap from './customer-form-map';
import ChangePasswordHandler from '../../components/change-password-handler';

/**
 * Class responsible for javascript actions in customer add/edit page.
 */
export default class CustomerForm {
  constructor() {
    // Watch password field change and strength indicator
    const passwordHandler = new ChangePasswordHandler(
      customerFormMap.passwordStrengthFeedbackContainer,
    );
    passwordHandler.watchPasswordStrength($(customerFormMap.passwordInput));

    // Watch customer group checkbox change and if it was unchecked,
    // update default group below if it's no longer in the list.
    $(customerFormMap.customerGroupCheckboxes).on('change', (event) => {
      this.checkOrUpdateDefaultGroup($(event.currentTarget).is(':checked'));
    });

    // Watch is_guest switch change and update other inputs accordingly
    $(customerFormMap.isGuestRadios).on('change', (event) => {
      if (Number($(event.currentTarget).val()) === 1) {
        this.adaptFormForGuestCustomer();
      } else {
        this.adaptFormForRegisteredCustomer();
      }
    });
  }

  private checkOrUpdateDefaultGroup(wasChecked: boolean): void {
    // Get currently selected group ID
    const currentDefaultGroup = Number(<string> $(customerFormMap.defaultGroupSelectedOption).val());

    // Get all checked groups in group access
    const checkedGroups: number[] = [];
    let firstGroupInList: number = 0;
    $(customerFormMap.customerGroupCheckboxes).each((index, input) => {
      const groupId = Number(<string> $(input).val());

      // We will keep track of all checked groups
      if ($(input).is(':checked')) {
        checkedGroups.push(groupId);
      }
      // And store ID of the first group regardless of it's status
      if (index === 0) {
        firstGroupInList = groupId;
      }
    });

    // If no groups are selected, use the first group in the list, no matter
    // if it's selected or not.
    if (!checkedGroups.length) {
      $(customerFormMap.defaultGroupSelect).val(firstGroupInList).trigger('change');
      return;
    }

    // If the last change was a newly added group and it's the only one in the list,
    // we will set it as the default group.
    if (wasChecked && checkedGroups.length === 1) {
      $(customerFormMap.defaultGroupSelect).val(checkedGroups[0]).trigger('change');
      return;
    }

    // If the default group is not in the list anymore, select the first checked group.
    if (!checkedGroups.includes(currentDefaultGroup)) {
      $(customerFormMap.defaultGroupSelect).val(checkedGroups[0]).trigger('change');
    }
  }

  private adaptFormForGuestCustomer(): void {
    // Disable password input and clean it
    $(customerFormMap.passwordInput)
      .prop('disabled', 'disabled')
      .prop('required', false)
      .val('')
      .removeClass('border-danger')
      .removeClass('border-success')
      .popover('dispose');

    // Hide password feedback
    $(customerFormMap.passwordStrengthFeedbackContainer).toggleClass('d-none', true);

    // Check groups and disable all checkboxes except guest group
    $(customerFormMap.customerGroupCheckboxes).each((index, input) => {
      if (Number($(input).val()) === window.data.guestGroupId) {
        $(input).prop('checked', 'checked');
      } else {
        $(input).prop('checked', false);
      }
      $(input).prop('disabled', 'disabled');
    });

    // Disable select all selector
    $('.js-choice-table-select-all').prop('disabled', 'disabled');

    // Set guest default group and disable the field
    $(customerFormMap.defaultGroupSelect).prop('disabled', 'disabled').val(window.data.guestGroupId).trigger('change');

    // Disable "Enabled" input and set it to yes
    $(customerFormMap.isEnabledRadios).prop('disabled', 'disabled');
    $(customerFormMap.isEnabledRadiosOff).prop('checked', false);
    $(customerFormMap.isEnabledRadiosOn).prop('checked', 'checked');
  }

  private adaptFormForRegisteredCustomer(): void {
    // Enable password input
    $(customerFormMap.passwordInput)
      .prop('disabled', false)
      .prop('required', 'required');

    // Check default groups and enable all checkboxes
    $(customerFormMap.customerGroupCheckboxes).each((index, input) => {
      if (window.data.defaultGroups.includes(Number($(input).val()))) {
        $(input).prop('checked', 'checked');
      } else {
        $(input).prop('checked', false);
      }
      $(input).prop('disabled', false);
    });

    // Enable select all selector
    $('.js-choice-table-select-all').prop('disabled', false);

    // Set customer group as default group and enable the field
    $(customerFormMap.defaultGroupSelect).prop('disabled', false)
      .val(window.data.customerGroupId).trigger('change');

    // Enable "Enabled" input and set it to yes
    $(customerFormMap.isEnabledRadios).prop('disabled', false);
    $(customerFormMap.isEnabledRadiosOff).prop('checked', false);
    $(customerFormMap.isEnabledRadiosOn).prop('checked', 'checked');
  }
}
