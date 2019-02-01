/**
 * 2007-2019 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class TabPermissionsConfigurator {
  constructor() {
    this.$table = $('#tabPermissionsTable');

    this.$table.on('change', '.js-tab-permissions-checkbox', (event) => {
      this._updatePermissions($(event.currentTarget));
    });

    return {};
  }

  _updatePermissions($checkbox) {
    let tabId, profileId, permission;

    [tabId, profileId, permission] = $checkbox.data('rel').split('||');

    const parentId = $checkbox.data('parent');
    const isChecked = $checkbox.is(':checked');

    console.log(parentId);

    // preselect parent
    if (0 !== parentId) {
      const $parentCheckbox = this.$table.find(`.js-checkbox-tab-${parentId}-permission-${permission}`);

      if ($parentCheckbox.is(':checked')) {

      } else if (isChecked && this._isChildrenChecked()) {
        $parentCheckbox.prop('checked', false).change();
      }
    }

    $.ajax(this.$table.data('url'), {
      method: 'POST',
      data: {
        profile_id: profileId,
        tab_id: tabId,
        permission: permission,
        expected_status: isChecked
      }
    }).then(response => {
      if (response.success) {
        showSuccessMessage(this.$table.data('success-message'));

        return;
      }

      showErrorMessage(this.$table.data('error-message'));
    });
  }

  _isChildrenChecked() {

  }
}
