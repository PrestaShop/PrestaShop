/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {EventEmitter} from '@components/event-emitter';
import ComponentsMap from '@components/components-map';

const {$} = window;

/**
 * This class is used to automatically toggle translated fields (displayed with tabs
 * using the TranslateType Symfony form type).
 * Also compatible with TranslatableInput changes.
 */
class TranslatableField {
  localeButtonSelector: string;

  localeNavigationSelector: string;

  translationFieldSelector: string;

  selectedLocale: string;

  constructor(options: Record<string, any>) {
    const opts = options || {};

    this.localeButtonSelector = opts.localeButtonSelector || ComponentsMap.translatableField.toggleTab;
    this.localeNavigationSelector = opts.localeNavigationSelector || ComponentsMap.translatableField.nav;
    this.translationFieldSelector = opts.translationFieldSelector || ComponentsMap.translatableField.select;
    this.selectedLocale = $(
      '.nav-item a.active',
      $(this.localeNavigationSelector),
    ).data('locale');

    $('body').on(
      'shown.bs.tab',
      this.localeButtonSelector,
      this.toggleLanguage.bind(this),
    );
    EventEmitter.on('languageSelected', this.toggleFields.bind(this));
  }

  /**
   * @param form
   *
   * @private
   */
  private refreshInputs(form: JQuery<Element>) {
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
    const localeLink = $(event.target);
    const form = localeLink.closest('form');
    this.selectedLocale = localeLink.data('locale');
    this.refreshInputs(form);
  }

  /**
   * Toggle all transtation fields to the selected locale
   *
   * @param event
   *
   * @private
   */
  toggleFields(event: Record<string, string>): void {
    this.selectedLocale = event.selectedLocale;

    $(this.localeNavigationSelector).each((index, navigation) => {
      const selectedLink = $('.nav-item a.active', navigation);
      const selectedLocale = selectedLink.data('locale');

      if (this.selectedLocale !== selectedLocale) {
        $(
          ComponentsMap.translatableField.specificLocale(this.selectedLocale),
          navigation,
        ).tab('show');
      }
    });
  }
}

export default TranslatableField;
