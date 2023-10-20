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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    this.shopChoiceTree = new ChoiceTree(this.shopChoiceTreeSelector);
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
    );

    const passwordHandler = new ChangePasswordHandler(
      employeeFormMap.passwordStrengthFeedbackContainer,
    );
    passwordHandler.watchPasswordStrength($(employeeFormMap.passwordInput));

    this.initEvents();
    this.toggleShopTree();
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
}
