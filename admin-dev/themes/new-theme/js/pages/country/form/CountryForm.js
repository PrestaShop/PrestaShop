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

import countryFormMap from './../country-map';

/**
 * Class responsible for javascript actions in country add/edit page.
 */
export default class CountryForm {
  constructor() {
    this.lastLayoutModified = null;

    this.initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  initEvents() {
    $(countryFormMap.addressFormat.addPatternBtn).on('click', (event) => this.handlePatternClick(event));
    $(countryFormMap.addressFormat.customAddressFieldsTabBtn).on('click', (event) => this.handleTabShowClick(event));
    $(countryFormMap.addressFormat.formatTextAreaField).on('keyup', (event) => this.saveLastModified(event));
    $(countryFormMap.addressFormat.modifyAddressLayoutBtn).on('click', (event) => this.modifyLayout(event));
  }

  /**
   * Handles pattern link click for address format text input
   *
   * @param event
   */
  handlePatternClick(event) {
    this.addFieldsToCursorPosition($(event.target).attr('id'));
    this.lastLayoutModified = $(countryFormMap.addressFormat.formatTextAreaField).val();
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
    if (pattern === undefined) {
      return;
    }

    const $element = $(countryFormMap.addressFormat.formatTextAreaField).get(0);
    let position = 0;

    if ('selectionStart' in $element) {
      position = $element.selectionStart;
    } else if ('selection' in document) {
      $element.focus();
      const sel = document.selection.createRange();
      const selLength = document.selection.createRange().text.length;

      sel.moveStart('character', -$element.value.length);
      position = sel.text.length - selLength;
    }

    const content = $(countryFormMap.addressFormat.formatTextAreaField).val();
    $(countryFormMap.addressFormat.formatTextAreaField).val(`${content.substr(0, position)} ${pattern} ${content.substr(position)}`);
  }

  /**
   * Resets address format text box value to default layout or last modified
   *
   * @param event
   */
  modifyLayout(event) {
    const confirmation = $(event.target).data('confirmationMessage');
    let defaultLayout = $(event.target).data('defaultLayout');

    if (typeof defaultLayout === 'undefined') {
      defaultLayout = this.lastLayoutModified;
    }

    if (defaultLayout !== null) {
      if (confirm(confirmation)) {
        $(countryFormMap.addressFormat.formatTextAreaField).val(unescape(defaultLayout.replace(/\+/g, ' ')));
      }
    }
  }
}
