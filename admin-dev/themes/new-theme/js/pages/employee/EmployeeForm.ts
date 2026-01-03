/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ChoiceTree from '../../components/form/choice-tree';
import AddonsConnector from '../../components/addons-connector';
import ChangePasswordControl from '../../components/form/change-password-control';
import employeeFormMap from './employee-form-map';
import ChangePasswordHandler from '../../components/change-password-handler';

/**
 * Class responsible for javascript actions in employee add/edit page.
 */
export default class EmployeeForm {
  shopChoiceTreeSelector: string;

  shopChoiceTree: ChoiceTree;

  employeeProfileSelector: string;

  tabsDropdownSelector: string;

  constructor() {
    this.shopChoiceTreeSelector = employeeFormMap.shopChoiceTree;
    this.shopChoiceTree = new window.prestashop.component.ChoiceTree(this.shopChoiceTreeSelector);
    this.employeeProfileSelector = employeeFormMap.profileSelect;
    this.tabsDropdownSelector = employeeFormMap.defaultPageSelect;

    this.shopChoiceTree.enableAutoCheckChildren();

    new AddonsConnector(
      employeeFormMap.addonsConnectForm,
      employeeFormMap.addonsLoginButton,
    );

    new ChangePasswordControl(
      employeeFormMap.changePasswordInputsBlock,
      employeeFormMap.showChangePasswordBlockButton,
      employeeFormMap.hideChangePasswordBlockButton,
      employeeFormMap.oldPasswordInput,
      employeeFormMap.newPasswordInput,
      employeeFormMap.confirmNewPasswordInput,
      employeeFormMap.generatedPasswordDisplayInput,
      employeeFormMap.passwordStrengthFeedbackContainer,
      employeeFormMap.generatedPasswordButton,
    );

    const passwordHandler = new ChangePasswordHandler(
      employeeFormMap.passwordStrengthFeedbackContainer,
    );
    passwordHandler.watchPasswordStrength($(employeeFormMap.passwordInput));

    this.initEvents();
    this.toggleShopTree();
    this.toggleTwoFactorFields();
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  private initEvents(): void {
    const $employeeProfilesDropdown = $(this.employeeProfileSelector);
    const getTabsUrl = $employeeProfilesDropdown.data('get-tabs-url');

    $(document).on('change', this.employeeProfileSelector, () => this.toggleShopTree(),
    );

    // Reload tabs dropdown when employee profile is changed.
    $(document).on('change', this.employeeProfileSelector, (event) => {
      const $tabsDropdown = $(this.tabsDropdownSelector);
      $tabsDropdown.empty();
      $tabsDropdown.prop('disabled', true);
      $.get(
        getTabsUrl,
        {
          profileId: $(event.currentTarget).val(),
        },
        (tabs) => {
          this.reloadTabsDropdown(tabs);
        },
        'json',
      );
    });

    $(document).on('change', `input[name="${employeeFormMap.twoFactorEnabledName}"]`, () => {
      this.toggleTwoFactorFields();
    });

    $(document).on('change', `input[name="${employeeFormMap.twoFactorTotpEnabledName}"]`, () => {
      this.toggleTwoFactorFields();
    });

    $(document).on('change', `input[name="${employeeFormMap.twoFactorEmailEnabledName}"]`, () => {
      this.toggleTwoFactorFields();
    });
  }

  /**
   * Reload tabs dropdown with new content.
   *
   * @param {Object} accessibleTabs
   *
   * @private
   */
  private reloadTabsDropdown(accessibleTabs: HTMLElement): void {
    const $tabsDropdown = $(this.tabsDropdownSelector);

    $tabsDropdown.empty();

    Object.values(accessibleTabs).forEach((accessibleTab) => {
      if (accessibleTab.children.length > 0 && accessibleTab.name) {
        // If tab has children - create an option group and put children inside.
        const $optgroup = this.createOptionGroup(accessibleTab.name);

        Object.keys(accessibleTab.children).forEach((childKey) => {
          if (accessibleTab.children[childKey].name) {
            $optgroup.append(
              this.createOption(
                accessibleTab.children[childKey].name,
                accessibleTab.children[childKey].id_tab,
              ),
            );
          }
        });

        $tabsDropdown.append($optgroup);
      } else if (accessibleTab.name) {
        // If tab doesn't have children - create an option.
        $tabsDropdown.append(
          this.createOption(accessibleTab.name, accessibleTab.id_tab),
        );
      }
    });
    $tabsDropdown.prop('disabled', false);
    // The default page field is rendered as a select2 component, which keeps its own rendering:
    // trigger change so it re-syncs with the rebuilt options instead of showing the previous role's list.
    $tabsDropdown.trigger('change');
  }

  /**
   * Hide shop choice tree if superadmin profile is selected, show it otherwise.
   *
   * @private
   */
  private toggleShopTree(): void {
    const $employeeProfileDropdown = $(this.employeeProfileSelector);
    const superAdminProfileId = $employeeProfileDropdown.data('admin-profile');
    $(this.shopChoiceTreeSelector)
      .closest('.form-group')
      .toggleClass(
        'd-none',
        $employeeProfileDropdown.val() === superAdminProfileId,
      );
  }

  /**
   * Creates an <optgroup> element
   *
   * @param {String} name
   *
   * @returns {jQuery}
   *
   * @private
   */
  private createOptionGroup(name: string): JQuery {
    return $(`<optgroup label="${name}">`);
  }

  /**
   * Creates an <option> element.
   *
   * @param {String} name
   * @param {String} value
   *
   * @returns {jQuery}
   *
   * @private
   */
  private createOption(name: string, value: string): JQuery {
    return $(`<option value="${value}">${name}</option>`);
  }

  /**
   * Returns true if the ps-switch radio "Yes" is selected.
   */
  private isPsSwitchEnabledByName(inputName: string): boolean {
    return $(`input[name="${inputName}"]:checked`).val() === '1';
  }

  /**
   * Hide/show the whole .form-group row that contains the given wrapper selector.
   * Your markup contains <span class="ps-switch" id="employee_two_factor_enabled">...</span>
   */
  private toggleSwitchFormGroup(wrapperSelector: string, show: boolean): void {
    const $wrapper = $(wrapperSelector);

    if ($wrapper.length === 0) {
      return;
    }

    $wrapper.closest('.form-group.row').toggleClass('d-none', !show);
  }

  private toggleInputFormGroup(inputSelector: string, show: boolean): void {
    const $input = $(inputSelector);

    if ($input.length === 0) {
      return;
    }

    $input.closest('.form-group.row').toggleClass('d-none', !show);
  }

  /**
   * Toggle 2FA fields according to current selection.
   * - If 2FA disabled => hide TOTP & Email provider switches
   * - If 2FA enabled => show provider switches
   */
  private toggleTwoFactorFields(): void {
    if ($(employeeFormMap.twoFactorEnabledWrapper).length === 0) {
      return;
    }

    const is2faEnabled = this.isPsSwitchEnabledByName(employeeFormMap.twoFactorEnabledName);

    this.toggleSwitchFormGroup(employeeFormMap.twoFactorTotpEnabledWrapper, is2faEnabled);
    this.toggleSwitchFormGroup(employeeFormMap.twoFactorEmailEnabledWrapper, is2faEnabled);

    if (!is2faEnabled) {
      this.toggleInputFormGroup(employeeFormMap.twoFactorProvisioningUriInput, false);
      this.toggleInputFormGroup(employeeFormMap.twoFactorTotQrCode, false);
      this.toggleInputFormGroup(employeeFormMap.twoFactorTotCode, false);
      return;
    }

    const isTotpEnabled = this.isPsSwitchEnabledByName(employeeFormMap.twoFactorTotpEnabledName);

    this.toggleInputFormGroup(employeeFormMap.twoFactorProvisioningUriInput, isTotpEnabled);
    this.toggleInputFormGroup(employeeFormMap.twoFactorTotQrCode, isTotpEnabled);
    this.toggleInputFormGroup(employeeFormMap.twoFactorTotCode, isTotpEnabled);
  }
}
