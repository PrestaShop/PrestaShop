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
