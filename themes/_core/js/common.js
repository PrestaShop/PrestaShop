/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';

export function psShowHide() {
  $('.ps-shown-by-js').show();
  $('.ps-hidden-by-js').hide();
}

/**
 * This function returns the value of the requested parameter from the URL
 * @param {string} paramName - the name of the requested parameter
 * @returns {string|null|object}
 */
export function psGetRequestParameter(paramName) {
  const vars = {};
  window.location.href.replace(location.hash, '').replace(/[?&]+([^=&]+)=?([^&]*)?/gi, (m, key, value) => {
    vars[key] = value !== undefined ? value : '';
  });
  if (paramName !== undefined) {
    return vars[paramName] ? vars[paramName] : null;
  }

  return vars;
}

/**
 * on checkout page, when we get the refresh flag :
 * on payment step we need to refresh page to be sure
 * amount is correctly updated on payment modules
 */
export function refreshCheckoutPage() {
  const queryParams = psGetRequestParameter();

  // we get the refresh flag : on payment step we need to refresh page to be sure
  // amount is correctly updated on payemnt modules
  if (queryParams.updatedTransaction) {
    // this parameter is used to display some info message
    // already set : just refresh page
    window.location.reload();

    return;
  }

  // not set : add it to the url
  queryParams.updatedTransaction = 1;

  const joined = Object.entries(queryParams)
    .map((v) => v.join('='))
    .join('&');
  window.location.href = `${window.location.pathname}?${joined}`;
}

/**
 * Verify password score.
 * Estimate guesses needed to crack the password.
 */
prestashop.checkPasswordScore = async(password) => {
  const { zxcvbn, zxcvbnOptions } = await import('@zxcvbn-ts/core');
  const zxcvbnCommonPackage = await import('@zxcvbn-ts/language-common');
  const zxcvbnEnPackage = await import('@zxcvbn-ts/language-en');

  const options = {
    translations: zxcvbnEnPackage.translations,
    graphs: zxcvbnCommonPackage.adjacencyGraphs,
    dictionary: {
      ...zxcvbnCommonPackage.dictionary,
      ...zxcvbnEnPackage.dictionary,
    },
  };

  zxcvbnOptions.setOptions(options);

  return zxcvbn(password);
};
