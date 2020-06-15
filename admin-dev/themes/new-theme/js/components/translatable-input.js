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

import {EventEmitter} from './event-emitter';

const $ = window.$;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */
class TranslatableInput {
  constructor(options) {
    options = options || {};

    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

    $('body').on('click', this.localeItemSelector, this.toggleLanguage.bind(this));
    EventEmitter.on('languageSelected', this.toggleInputs.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */
  toggleLanguage(event) {
    const localeItem = $(event.target);
    const form = localeItem.closest('form');
    EventEmitter.emit('languageSelected', {
      selectedLocale: localeItem.data('locale'),
      form: form
    });
  }

  /**
   * Toggle all translatable inputs in form in which locale was changed
   *
   * @param {Event} event
   */
  toggleInputs(event) {
    const form = event.form;
    const selectedLocale = event.selectedLocale;
    const localeButton = form.find(this.localeButtonSelector);
    const changeLanguageUrl = localeButton.data('change-language-url');

    localeButton.text(selectedLocale);
    form.find(this.localeInputSelector).addClass('d-none');
    form.find(`${this.localeInputSelector}.js-locale-${selectedLocale}`).removeClass('d-none');

    if (changeLanguageUrl) {
      this._saveSelectedLanguage(changeLanguageUrl, selectedLocale);
    }
  }

  /**
   * Save language choice for employee forms.
   *
   * @param {String} changeLanguageUrl
   * @param {String} selectedLocale
   *
   * @private
   */
  _saveSelectedLanguage(changeLanguageUrl, selectedLocale) {
    $.post({
      url: changeLanguageUrl,
      data: {
        language_iso_code: selectedLocale
      }
    });
  }
}

export default TranslatableInput;
