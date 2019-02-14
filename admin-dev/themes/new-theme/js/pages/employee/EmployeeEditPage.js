/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import ChoiceTree from "../../components/form/choice-tree";
import AddonsConnector from "../../components/addons-connector";
import ChangePasswordControl from "../../components/form/change-password-control";

/**
 * Class responsible for javascript actions in employee edit page.
 */
export default class EmployeeEditPage {
  constructor() {
    this.shopChoiceTree = new ChoiceTree('#employee_shop_association');
    this.employeeProfileSelector = '#employee_profile';
    this.tabsDropdownSelector = '#employee_default_page';

    this.shopChoiceTree.enableAutoCheckChildren();
    new AddonsConnector('#addons-connect-form', '#addons_login_btn');
    new ChangePasswordControl();

    this.initEvents();
  }

  /**
   * Initialize page's events.
   */
  initEvents() {
    const $employeeProfilesDropdown = $(this.employeeProfileSelector);
    const superAdminProfileId = $employeeProfilesDropdown.data('admin-profile');
    const getTabsUrl = $employeeProfilesDropdown.data('get-tabs-url');
    const t = this;

    $(document).on('change', this.employeeProfileSelector, function () {
      // Disable shop choice tree if superadmin profile is selected.
      $(this).val() == superAdminProfileId ?
        t.shopChoiceTree.disableAllInputs() :
        t.shopChoiceTree.enableAllInputs();
    });

    // Reload tabs dropdown when employee profile is changed.
    $(document).on('change', this.employeeProfileSelector, function () {
      $.get(
        getTabsUrl,
        {
          profileId: $(this).val()
        },
        (tabs) => {
          t.reloadTabsDropdown(tabs);
        },
        'json'
      );
    });
  }

  /**
   * Reload tabs dropdown with new content.
   *
   * @param {Object} accessibleTabs
   */
  reloadTabsDropdown(accessibleTabs) {
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
   * Creates an <optgroup> element
   *
   * @param {String} name
   *
   * @returns {jQuery}
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
   */
  _createOption(name, value) {
    return $(`<option value="${value}">${name}</option>`);
  }
}
