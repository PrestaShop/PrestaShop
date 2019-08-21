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

import countryFormMap from "./country-form-map";

/**
 * Class responsible for javascript actions in country add/edit page.
 */
export default class CountryForm {
  constructor() {
    this.customAddressFields = countryFormMap.customAddressFields;
    this.patternSelect = countryFormMap.patternSelect;
    this.formatInput = countryFormMap.formatInput;
    this.eraseCurrentLayout = countryFormMap.eraseCurrentLayout;
    this.lastLayoutModified = null;

    this._initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    $(this.patternSelect).on('click', (event) => this.handlePatternClick(event));
    $(this.customAddressFields).on('click', (event) => this.handleTabShowClick(event));
    $(this.formatInput).on('keyup', (event) => this.saveLastModified(event));
    $(this.eraseCurrentLayout).on('click', (event) => this.resetLayout(event));
  }

  /**
   * Handles pattern link click for address format text input
   *
   * @param event
   */
  handlePatternClick(event) {
    this.addFieldsToCursorPosition($(event.target).attr("id"));
    this.lastLayoutModified = $(this.formatInput).val();
  }

  /**
   * Saves last user input for use in user current modified format action
   *
   * @param event
   */
  saveLastModified(event) {
    this.lastLayoutModified = $(event.target).val();
  }

  /**
   * Handles address format tabs switching
   *
   * @param event
   */
  handleTabShowClick(event) {
    event.preventDefault();
    $(event.target).tab('show');
  }

  /**
   * Adds pattern to a cursor position in format text box
   *
   * @param pattern
   */
  addFieldsToCursorPosition(pattern) {
    const el = $(this.formatInput).get(0);
    let pos = 0;

    if ('selectionStart' in el) {
      pos = el.selectionStart;
    } else if ('selection' in document) {
      el.focus();
      const sel = document.selection.createRange();
      const selLength = document.selection.createRange().text.length;

      sel.moveStart('character', -el.value.length);
      pos = sel.text.length - selLength;
    }

    const content = $(this.formatInput).val();
    $(this.formatInput).val(content.substr(0, pos) + pattern + ' ' + content.substr(pos));
  }

  /**
   * Resets address format text box value to default layout or last modified
   *
   * @param event
   */
  resetLayout(event) {
    const confirmation = $(event.target).data('confirmation');
    let defaultLayout = $(event.target).data('default-layout');

    if (typeof defaultLayout === 'undefined') {
      defaultLayout = this.lastLayoutModified;
    }

    if (defaultLayout !== null) {
      if (confirm(confirmation)) {
        $(this.formatInput).val(unescape(defaultLayout.replace(/\+/g, ' ')));
      }
    }
  }
}
