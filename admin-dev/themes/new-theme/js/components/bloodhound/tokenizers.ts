/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import _ from 'lodash';
// @ts-ignore
import Bloodhound from 'typeahead.js';

/**
 * This comes from Bloodhound it allows to create tokenizer based on multiple fields from an object.
 *
 * @param tokenizer
 * @returns {function(*=, ...[*]=): function(*): *[]}
 */
function getObjTokenizer(tokenizer: any) {
  return function setKey(...args: any) {
    const tokenizerKeys = _.isArray(args[0]) ? args[0] : args;

    return function tokenize(val: Array<string>) {
      let tokens: Array<string> = [];
      tokenizerKeys.forEach((key: number) => {
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
export const letters = (val: any): Array<string> => {
  const tokens = Bloodhound.tokenizers.nonword(val);
  tokens.forEach((token: string) => {
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
