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

/**
 * Generates random values for inputs.
 *
 * Usage:
 *
 * There should be a button in HTML with 2 required data-* properties:
 *    1. data-target-input-id - input id for which value should be generated
 *    2. data-generated-value-size -
 *
 * Example button: <button class="js-generator-btn"
 *                         data-target-input-id="my-input-id"
 *                         data-generated-value-length="16"
 *                 >
 *                     Generate!
 *                 </button>
 *
 * In JavaScript you have to enable this functionality using GeneratableInput component like so:
 *
 * const generateableInput = new GeneratableInput();
 * generateableInput.attachOn('.js-generator-btn'); // every time our button is clicked
 *                                                  // it will generate random value of 16 characters
 *                                                  // for input with id of "my-input-id"
 *
 * You can attach as many different buttons as you like using "attachOn()" function
 * as long as 2 required data-* attributes are present at each button.
 */
export default class GeneratableInput {
  constructor() {
    return {
      'attachOn': (btnSelector) => this._attachOn(btnSelector),
    };
  }

  /**
   * Attaches event listener on button than can generate value
   *
   * @param {String} generatorBtnSelector
   *
   * @private
   */
  _attachOn(generatorBtnSelector) {
    document.querySelector(generatorBtnSelector).addEventListener('click', (event) => {
      const attributes = event.currentTarget.attributes;

      const targetInputId = attributes.getNamedItem('data-target-input-id').value;
      const generatedValueLength = parseInt(attributes.getNamedItem('data-generated-value-length').value);

      const targetInput = document.querySelector('#' + targetInputId);
      targetInput.value = this._generateValue(generatedValueLength)
    });
  }

  /**
   * Generates random value for input
   *
   * @param {Number} length
   *
   * @returns {string}
   *
   * @private
   */
  _generateValue(length) {
    const chars = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    let generatedValue = '';

    for (let i = 1; i <= length; ++i) {
      generatedValue += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return generatedValue;
  }
}
