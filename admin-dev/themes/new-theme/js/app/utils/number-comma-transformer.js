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

import _ from 'lodash';

const findAllUnwantedCharsExceptTheLatestOne = /[^\d]+(?=.*[^\d])/g;
const findAllUnwantedChars = /([^\d]+)/g;

/**
 * Same explode function as on PHP
 */
const explode = (string, separator, limit) => {
  const array = string.split(separator);
  if (limit !== undefined && array.length >= limit) {
    array.push(array.splice(limit - 1).join(separator));
  }

  return array;
};

const clearNumberInputValue = (event, selector) => {
  if (!event.target.matches(selector)) {
    return;
  }

  let value = event.target.value;

  /**
   * If there is a dot in the string
   * split the string at the first dot, and
   * replace all unwanted characters.
   * Otherwise, replace all unwanted characters expect the
   * latest one, and remove the latest character
   * by a dot.
   */
  if (value.indexOf('.') !== -1) {
    value = explode(value, '.', 2)
      .map(num => num.replace(findAllUnwantedChars, ''))
      .join('.');
  } else {
    value = value
      .replace(findAllUnwantedCharsExceptTheLatestOne, '')
      .replace(findAllUnwantedChars, '.');
  }

  event.target.value = value;
};

export default (selector) => {
  document.addEventListener(
    'keyup',
    _.debounce(
      (event) => {
        clearNumberInputValue(event, selector);
      },
      500,
  ));
};
