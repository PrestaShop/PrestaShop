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
import Bloodhound from 'typeahead.js';

/**
 * This comes from Bloodhound it allows to create tokenizer based on multiple fields from an object.
 *
 * @param tokenizer
 * @returns {function(*=, ...[*]=): function(*): *[]}
 */
function getObjTokenizer(tokenizer) {
  return function setKey(keys, ...args) {
    const tokenizerKeys = _.isArray(keys) ? keys : [].slice.call(args, 0);

    return function tokenize(val) {
      let tokens = [];
      tokenizerKeys.forEach((key) => {
        tokens = tokens.concat(tokenizer(_.toString(val[key])));
      });

      return tokens;
    };
  };
}

/**
 * Split the word into multiple tokens ok different sizes, thus allowing to search into parts of the words,
 * the min length of a token is two letters though (maybe it could be configurable in the future)
 *
 * @param {string} val
 *
 * @return {array}
 */
export const letters = (val) => {
  const tokens = Bloodhound.tokenizers.nonword(val);
  tokens.forEach((token) => {
    let i = 0;
    while (i + 1 < token.length) {
      tokens.push(token.substr(i, token.length));
      i += 1;
    }
  });

  return tokens;
};

export default {
  letters,
  obj: {
    letters: getObjTokenizer(letters),
  },
};
