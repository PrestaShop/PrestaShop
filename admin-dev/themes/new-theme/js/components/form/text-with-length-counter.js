/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * TextWithLengthCounter handles input with length counter UI.
 */
export default class TextWithLengthCounter {
    constructor() {
        $(document).on(
            'input',
            '.js-text-with-counter-input-group input[type="text"]',
            e => {
                const $input = $(e.currentTarget);
                const remainingLength =
                    $input.data('max-length') - $input.val().length;

                $input
                    .closest('.js-text-with-counter-input-group')
                    .find('.js-counter-text')
                    .text(remainingLength);
            },
        );
    }

    /**
     * Check/uncheck all boxes in table
     *
     * @param {Event} event
     */
    handleSelectAll(event) {
        const $selectAllCheckboxes = $(event.target);
        const isSelectAllChecked = $selectAllCheckboxes.is(':checked');

        $selectAllCheckboxes
            .closest('table')
            .find('tbody input:checkbox')
            .prop('checked', isSelectAllChecked);
    }
}
