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

/**
 * Manages module permissions configuration table
 */
export default class ModulePermissionsConfigurator {
  constructor() {
    this.$table = $('#modulePermissionsTable');

    this.$table.on('change', '.js-module-permission-checkbox', (event) => {
      this._updatePermissions($(event.currentTarget));
    });
  }

  /**
   * Update module permissions for profile via AJAX
   *
   * @param {jQuery} $checkbox
   *
   * @private
   */
  _updatePermissions($checkbox) {
    const isChecked = $checkbox.is(':checked');
    let moduleId, permission, profileId;

    [moduleId, permission, profileId] = $checkbox.data('rel').split('||');

    $.ajax(this.$table.data('url'), {
      method: 'POST',
      data: {
        profile_id: profileId,
        module_id: moduleId,
        permission: permission,
        expected_status: isChecked
      }
    }).then((response) => {
      if (response.success) {
        showSuccessMessage(this.$table.data('success-message'));

        return;
      }

      showErrorMessage(this.$table.data('error-message'));
    });
  }
}
