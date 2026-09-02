/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * This formatter is used by VueI18n, the basic format for variables looks
 * like 'Hi {name}' or 'Hi {0}' Sadly it doesn't match the PrestaShop usual
 * placeholders format.
 * So this custom formatter allows us to simple replace in order to use formats
 * like 'Hi %name%' the parameters then should be an object like {'%name%': 'John'}
 */
export default class ReplaceFormatter {
  /**
   * @param message {string}
   * @param values {object}
   *
   * @returns {array}
   */
  interpolate(message, values) {
    if (!values) {
      return [message];
    }

    let msg = message;
    Object.keys(values).forEach((param) => {
      let placeholder = param;

      // If the param doesn't use PrestaShop formatting (with %) nor Symfony usual one (with {})
      // then we fallback to VueI18n usual one which uses `{param}`
      if (placeholder.indexOf('%') === -1 && placeholder.indexOf('{') === -1) {
        placeholder = `{${placeholder}}`;
      }
      msg = msg.replace(placeholder, values[param]);
    });

    return [msg];
  }
}
