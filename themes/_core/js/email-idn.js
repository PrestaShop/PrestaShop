/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import punycode from 'punycode';

const init = function initEmailFields(selector) {
  const $emailFields = $(selector);
  $.each($emailFields, (i, field) => {
    if (!field.checkValidity()) {
      const parts = field.value.split('@');

      if (punycode.toASCII(parts[0]) === parts[0]) {
        field.value = punycode.toASCII(field.value);
      }
    }
  });
};

export default init;
