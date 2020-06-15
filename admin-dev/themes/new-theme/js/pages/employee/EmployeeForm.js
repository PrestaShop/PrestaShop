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

import ChoiceTree from "../../components/form/choice-tree";
import AddonsConnector from "../../components/addons-connector";
import ChangePasswordControl from "../../components/form/change-password-control";
import employeeFormMap from "./employee-form-map";

/**
 * Class responsible for javascript actions in employee add/edit page.
 */
export default class EmployeeForm {
  constructor() {
    this.shopChoiceTreeSelector = employeeFormMap.shopChoiceTree;
    this.shopChoiceTree = new ChoiceTree(this.shopChoiceTreeSelector);
    this.employeeProfileSelector = employeeFormMap.profileSelect;
    this.tabsDropdownSelector = employeeFormMap.defaultPageSelect;

    this.shopChoiceTree.enableAutoCheckChildren();

    new AddonsConnector(
      employeeFormMap.addonsConnectForm,
      employeeFormMap.addonsLoginButton
    );

    new ChangePasswordControl(
      employeeFormMap.changePasswordInputsBlock,
      employeeFormMap.showChangePasswordBlockButton,
      employeeFormMap.hideChangePasswordBlockButton,
      employeeFormMap.generatePasswordButton,
      employeeFormMap.oldPasswordInput,
      employeeFormMap.newPasswordInput,
      employeeFormMap.confirmNewPasswordInput,
      employeeFormMap.generatedPasswordDisplayInput,
      employeeFormMap.passwordStrengthFeedbackContainer
    );

    this._initEvents();
    this._toggleShopTree();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    const $employeeProfilesDropdown = $(this.employeeProfileSelector);
    const getTabsUrl = $employeeProfilesDropdown.data('get-tabs-url');

    $(document).on('change', this.employeeProfileSelector, () => this._toggleShopTree());

    // Reload tabs dropdown when employee profile is changed.
    $(document).on('change', this.employeeProfileSelector, (event) => {
      $.get(
        getTabsUrl,
        {
          profileId: $(event.currentTarget).val()
        },
        (tabs) => {
          this._reloadTabsDropdown(tabs);
        },
        'json'
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
  _reloadTabsDropdown(accessibleTabs) {
    const $tabsDropdown = $(this.tabsDropdownSelector);

    $tabsDropdown.empty();

    for (let key in accessibleTabs) {
      if (accessibleTabs[key]['children'].length > 0 && accessibleTabs[key]['name']) {
        // If tab has children - create an option group and put children inside.
        const $optgroup = this._createOptionGroup(accessibleTabs[key]['name']);

        for (let childKey in accessibleTabs[key]['children']) {
          if (accessibleTabs[key]['children'][childKey]['name']) {
            $optgroup.append(
              this._createOption(
                accessibleTabs[key]['children'][childKey]['name'],
                accessibleTabs[key]['children'][childKey]['id_tab'])
            );
          }
        }

        $tabsDropdown.append($optgroup);
      } else if (accessibleTabs[key]['name']) {
        // If tab doesn't have children - create an option.
        $tabsDropdown.append(
          this._createOption(
            accessibleTabs[key]['name'],
            accessibleTabs[key]['id_tab']
          )
        );
      }
    }
  }

  /**
   * Hide shop choice tree if superadmin profile is selected, show it otherwise.
   *
   * @private
   */
  _toggleShopTree() {
    const $employeeProfileDropdown = $(this.employeeProfileSelector);
    const superAdminProfileId = $employeeProfileDropdown.data('admin-profile');
    $(this.shopChoiceTreeSelector)
      .closest('.form-group')
      .toggleClass('d-none', $employeeProfileDropdown.val() == superAdminProfileId)
    ;
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
  _createOptionGroup(name) {
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
  _createOption(name, value) {
    return $(`<option value="${value}">${name}</option>`);
  }
}
