/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import {EventEmitter} from './event-emitter';

const $ = window.$;

/**
 * This class is used to automatically toggle translated fields (displayed with tabs
 * using the TranslateType Symfony form type).
 * Also compatible with TranslatableInput changes.
 */
class TranslatableField {
  constructor(options) {
    options = options || {};

    this.localeButtonSelector = options.localeButtonSelector || '.translationsLocales.nav .nav-item a[data-toggle="tab"]';
    this.localeNavigationSelector = options.localeNavigationSelector || '.translationsLocales.nav';

    $('body').on('shown.bs.tab', this.localeButtonSelector, this.toggleLanguage.bind(this));
    EventEmitter.on('languageSelected', this.toggleFields.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */
  toggleLanguage(event) {
    const localeLink = $(event.target);
    const form = localeLink.closest('form');
    EventEmitter.emit('languageSelected', {selectedLocale: localeLink.data('locale'), form: form});
  }

  /**
   * Toggle all transtation fields to the selected locale
   *
   * @param event
   */
  toggleFields(event) {
    $(this.localeNavigationSelector).each((index, navigation) => {
      const selectedLink = $('.nav-item a.active', navigation);
      const selectedLocale = selectedLink.data('locale');
      if (event.selectedLocale !== selectedLocale) {
        $('.nav-item a[data-locale="'+event.selectedLocale+'"]', navigation).tab('show');
      }
    });
  }
}

export default TranslatableField;
