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

const $ = window.$;

/**
 * Component which allows to replace regular text to url friendly text
 *
 * Usage example in template:
 *
 * <input name="target-input" class="js-target-input"> // The original text will be taken from this element
 * <input name="destination-input" class="js-destination-input"> // Modified text will be added to this input
 *
 * in javascript:
 *
 * new ElementToLinkRewriteCopier({
 *   targetElementSelector: '.js-target-input'
 *   destinationElementSelector: '.js-destination-input',
 * });
 *
 * If the target-input has value "test name" the link rewrite value will be "test-name".
 * If the target-input has value "test name #$" link rewrite will be "test-name-" since #$ are un allowed characters in url.
 *
 * You can also pass additional options to change the event name, or encoding format:
 *
 * new ElementToLinkRewriteCopier({
 *   targetElementSelector: '.js-target-input'
 *   destinationElementSelector: '.js-destination-input',
 *   options: {
 *     eventName: 'change', // default is 'input'
 *     encoding: 'US-ASCII', //default is 'UTF-8'
 *   }
 * });
 *
 */
export default class ElementToLinkRewriteCopier {
  constructor({ targetElementSelector, destinationElementSelector, options = { eventName: 'input', encoding: 'UTF-8' } }) {

    $(document).on(options.eventName, `${targetElementSelector}`, (event) => {
      const $nameInput = $(event.currentTarget);
      const langId = this._getLanguageIdByElement($nameInput);
      let elementToModifySelector = destinationElementSelector;

      if (null !== langId) {
        elementToModifySelector = `${destinationElementSelector}[data-lang-id="${langId}"]`;
      }

      $(elementToModifySelector).val(str2url($nameInput.val(), options.encoding));
    })
  }

  /**
   * Gets language id by target element.
   *
   * @param {jQuery} $targetElement
   *
   * @returns {(null|number)}
   *
   * @private
   */
  _getLanguageIdByElement($targetElement) {
    const langId = $targetElement.attr('data-lang-id');

    return typeof langId === 'undefined' ? null : parseInt(langId);
  }
}
