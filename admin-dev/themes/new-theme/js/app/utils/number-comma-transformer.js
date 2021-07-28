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

const findAllUnwantedCharsExceptTheLatestOne = /(?:(?!^-\d+))[^\d]+(?=.*[^\d])/g;
const findAllUnwantedChars = /(?:(?!^-\d+))([^\d]+)/g;

/**
 * If there is a dot in the string
 * split the string at the first dot, and
 * replace all unwanted characters.
 * Otherwise, replace all unwanted characters expect the
 * latest one, and replace the latest character
 * by a dot.
 */
export const transform = (value) => {
  let val = value;
  const unwantedChars = val.match(findAllUnwantedChars);

  if (unwantedChars === null) {
    return val;
  }

  if (unwantedChars.length > 1) {
    const unique = [...new Set(unwantedChars)];

    if (unique.length === 1) {
      return val.replace(findAllUnwantedChars, '');
    }
  }

  val = val
    .replace(findAllUnwantedCharsExceptTheLatestOne, '')
    .replace(findAllUnwantedChars, '.');

  return val;
};

const clearNumberInputValue = (event, selector) => {
  if (!event.target.matches(selector)) {
    return;
  }

  const {value} = event.target;
  event.target.value = transform(value);
};

export default (selector) => {
  document.addEventListener(
    'change',
    (event) => {
      clearNumberInputValue(event, selector);
    },
    true,
  );
};
