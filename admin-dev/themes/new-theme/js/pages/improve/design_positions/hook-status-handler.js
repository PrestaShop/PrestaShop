/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const {$} = window;

class HookStatusHandler {
  constructor() {
    const self = this;
    self.$hookStatus = $('.hook-switch-action');
    self.$modulePositionsForm = $('#module-positions-form');

    self.$hookStatus.on('change', function (e) {
      e.stopImmediatePropagation();
      self.toogleHookStatus($(this));
    });
  }

  /**
     * Toogle hooks status
     */
  toogleHookStatus($hookElement) {
    $.ajax({
      type: 'POST',
      headers: {'cache-control': 'no-cache'},
      url: this.$modulePositionsForm.data('togglestatus-url'),
      data: {hookId: $hookElement.data('hook-id')},
      success(data) {
        if (data.status) {
          window.showSuccessMessage(data.message);
          const $hookModulesList = $hookElement.closest('.hook-panel').find('.module-list, .module-list-disabled');
          $hookModulesList.fadeTo(500, data.hook_status ? 1 : 0.5);
        } else {
          window.showErrorMessage(data.message);
        }
      },
    });
  }
}

export default HookStatusHandler;
