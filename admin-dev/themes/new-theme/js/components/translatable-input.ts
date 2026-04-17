/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {EventEmitter} from './event-emitter';

const {$} = window;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */
class TranslatableInput {
  localeItemSelector: string;

  localeButtonSelector: string;

  localeInputSelector: string;

  selectedLocale: string;

  constructor(options: Record<string, any> = {}) {
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
  }

  /**
   * @param {jQuery} form
   *
   * @private
   */
  refreshInputs(form: JQuery<Element>): void {
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
  toggleLanguage(event: JQueryEventObject): void {
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
  toggleInputs(event: Record<string, any>): void {
    const {form} = event;
    this.selectedLocale = event.selectedLocale;
    const localeButton = form.find(this.localeButtonSelector);
    const changeLanguageUrl = localeButton.data('change-language-url');

    localeButton.text(this.selectedLocale.toUpperCase());
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
  private saveSelectedLanguage(
    changeLanguageUrl: string,
    selectedLocale: string,
  ): void {
    $.post({
      url: changeLanguageUrl,
      data: {
        language_iso_code: selectedLocale,
      },
    });
  }
}

export default TranslatableInput;
