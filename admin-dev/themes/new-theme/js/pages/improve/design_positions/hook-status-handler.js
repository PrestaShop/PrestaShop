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
const {
    $
} = window;

class HookStatusHandler {
    constructor() {
        if ($('#position-filters').length === 0) {
            return;
        }

        const self = this;
        self.$hookStatus = $('.hook-switch-action');
        self.$modulePositionsForm = $('#module-positions-form');

        self.$hookStatus.on('change', function(e) {
            e.stopImmediatePropagation();
            self.toogleHookStatus($(this));
        });
    }

    /**
     * Toogle hooks status
     */
    toogleHookStatus($hookElement) {
        const self = this;

        $.ajax({
            type: 'POST',
            headers: {
                'cache-control': 'no-cache'
            },
            url: self.$modulePositionsForm.data('togglestatus-url'),
            data: {
                hookId: $hookElement.data('hook-id')
            },
            success: function(data) {
                if (data.status) {
                    window.showSuccessMessage(data.message);
                    var $hook_modules_list = $hookElement.closest('.hook-panel').find('.module-list');
                    console.log($hook_modules_list)
                    if (data.hook_status) {
                        $hook_modules_list.fadeTo(500, 1);
                    } else {
                        $hook_modules_list.fadeTo(500, 0.5);
                    }
                } else {
                    window.showErrorMessage(data.message);
                }
            },
        });
    }
}

export default HookStatusHandler;
