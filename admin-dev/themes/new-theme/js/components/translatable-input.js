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

const {$} = window;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */
class TranslatableInput {
  constructor(options) {
    const opts = options || {};

    this.localeItemSelector = opts.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = opts.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = opts.localeInputSelector || '.js-locale-input';
    this.selectedLocale = $(this.localeItemSelector).data('locale');

    $('body').on(
      'click',
      this.localeItemSelector,
      this.toggleLanguage.bind(this),
    );
    EventEmitter.on('languageSelected', this.toggleInputs.bind(this));

    return {
      localeItemSelector: this.localeItemSelector,
      localeButtonSelector: this.localeButtonSelector,
      localeInputSelector: this.localeInputSelector,

      /**
       * @param {jQuery} form
       */
      refreshFormInputs: (form) => { this.refreshInputs(form); },

      /**
       * @returns {string|undefined}
       */
      getSelectedLocale: () => this.selectedLocale,
    };
  }

  /**
   * @param {jQuery} form
   *
   * @private
   */
  refreshInputs(form) {
    if (!this.selectedLocale) {
      return;
    }

    EventEmitter.emit('languageSelected', {
      selectedLocale: this.selectedLocale,
      form,
    });
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   *
   * @private
   */
  toggleLanguage(event) {
    const localeItem = $(event.target);
    const form = localeItem.closest('form');
    this.selectedLocale = localeItem.data('locale');
    this.refreshInputs(form);
  }

  /**
   * Toggle all translatable inputs in form in which locale was changed
   *
   * @param {Event} event
   *
   * @private
   */
  toggleInputs(event) {
    const {form} = event;
    this.selectedLocale = event.selectedLocale;
    const localeButton = form.find(this.localeButtonSelector);
    const changeLanguageUrl = localeButton.data('change-language-url');

    localeButton.text(this.selectedLocale);
    form.find(this.localeInputSelector).addClass('d-none');
    form
      .find(`${this.localeInputSelector}.js-locale-${this.selectedLocale}`)
      .removeClass('d-none');

    if (changeLanguageUrl) {
      this.saveSelectedLanguage(changeLanguageUrl, this.selectedLocale);
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
  saveSelectedLanguage(changeLanguageUrl, selectedLocale) {
    $.post({
      url: changeLanguageUrl,
      data: {
        language_iso_code: selectedLocale,
      },
    });
  }
}

export default TranslatableInput;
