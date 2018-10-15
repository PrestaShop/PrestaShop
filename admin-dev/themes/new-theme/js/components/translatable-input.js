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

class TranslatableInput {
    constructor(options) {
        options = options || {};

        this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
        this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
        this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

        $('body').on('click', this.localeItemSelector, this.toggleInputs.bind(this));
    }

    /**
     * Toggle all translatable inputs in form in which locale was changed
     *
     * @param {Event} event
     */
    toggleInputs(event) {
        const localeItem = $(event.target);
        const form = localeItem.closest('form');
        const selectedLocale = localeItem.data('locale');

        form.find(this.localeButtonSelector).text(selectedLocale);
        form.find(this.localeInputSelector).addClass('d-none');
        form.find(this.localeInputSelector+'.js-locale-' + selectedLocale).removeClass('d-none');
    }
}

export default TranslatableInput;
