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
  constructor(tabPermissionsTableSelector) {
    this.$table = $(tabPermissionsTableSelector);

    this.$table.on('change', '.js-tab-permissions-checkbox', (event) => {
      this._updatePermissions($(event.currentTarget));
    });

    return {};
  }

  _updatePermissions($checkbox) {
    const checkboxData = this._getData($checkbox);

    this._toggleParent(checkboxData);
    this._handleAllPermissionColumn(checkboxData, $checkbox);
    this._handleMultiplePermissions(checkboxData);

    this._sendPermissions({
      profile_id: checkboxData.profileId,
      tab_id: checkboxData.tabId,
      permission: checkboxData.permission,
      expected_status: checkboxData.isChecked
    });
  }

  _isChildrenChecked(parentId, permission, rel) {
    const $children = this.$table.find(`[data-parent="${parentId}"][data-type="${permission}"]:not([data-rel="${rel}"])`);
    let isChecked = false;

    $children.each((i, element) => {
      if ($(element).is(':checked')) {
        isChecked = true;
      }
    });

    return isChecked;
  }

  _toggleParent(checkboxData) {
    if (0 !== checkboxData.parentTabId) {
      const $parentCheckbox = this.$table.find(`.js-tab-id-${checkboxData.parentTabId}.js-permission-${checkboxData.permission}`);
      console.log(`.js-parent-tab-id-${checkboxData.parentTabId}.js-permission-${checkboxData.permission}`);
      console.log($parentCheckbox.length);

      if (!$parentCheckbox.is(':checked')) {
        //$parentCheckbox.prop('checked', true).change();
        $parentCheckbox.attr('checked', true).change();
      } else if (!checkboxData.isChecked && !this._isChildrenChecked(checkboxData.parentTabId, checkboxData.permission, checkboxData.rel)) {
        //$parentCheckbox.prop('checked', false).change();
        $parentCheckbox.attr('checked', false).change();
      }
    }
  }

  _handleAllPermissionColumn(checkboxData, $checkbox) {
    const $row = $checkbox.closest('tr');
    if ('all' !== $checkbox.data('type') || !$row.hasClass('parent')) {
      return;
    }

    const $childCheckboxes = this.$table.find(`.js-child-${checkboxData.tabId} input[type="checkbox"]`);
    $childCheckboxes.attr('checked', checkboxData.isChecked);

    this._sendPermissions({
          profile_id: checkboxData.profileId,
          tab_id: checkboxData.tabId,
          permission: checkboxData.permission,
          expected_status: checkboxData.isChecked,
          from_parent: true,
    });
  }

  /**
   * Sends permissions to server for updating
   *
   * @param {Object} data
   *
   * @private
   */
  _sendPermissions(data) {
    $.ajax(this.$table.data('url'), {
      method: 'POST',
      data: data
    }).then(response => {
      if (response.success) {
        showSuccessMessage(this.$table.data('success-message'));

        return;
      }

      showErrorMessage(this.$table.data('error-message'));
    });
  }

  /**
   * Get permission data from checkbox
   *
   * @param {jQuery} $checkbox
   *
   * @private
   */
  _getData($checkbox) {
    let tabId, profileId, permission, tabSize, tabNumber;

    [tabId, profileId, permission, tabSize, tabNumber] = $checkbox.data('rel').split('||');

    return {
      tabId: tabId,
      parentTabId: $checkbox.data('parent'),
      profileId: profileId,
      permission: permission,
      isChecked: $checkbox.is(':checked'),
      tabSize: tabSize,
      tabNumber: tabNumber,
      rel: $checkbox.data('rel'),
    };
  }

  _handleMultiplePermissions(checkboxData) {
    // toggle all table
    if ('-1' === checkboxData.tabId && 'all' === checkboxData.permission) {
      this.$table.find('tbody .js-tab-permissions-checkbox').attr('checked', checkboxData.isChecked);

      return;
    }

    // toggle all row
    if ('all' === checkboxData.permission) {
      this.$table.find(`.js-tab-id-${checkboxData.tabId}`).attr('checked', checkboxData.isChecked);

      return;
    }

    // toggle all column
    if ('-1' === checkboxData.tabId) {
      this.$table.find(`.js-permission-${checkboxData.permission}`).attr('checked', checkboxData.isChecked);

      return;
    }
  }
}
