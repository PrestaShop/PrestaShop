/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
