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
import $ from 'jquery';

export function psShowHide () {
  $('.ps-shown-by-js').show();
  $('.ps-hidden-by-js').hide();
}

/**
 * This function returns the value of the requested parameter from the URL
 * @param {string} paramName - the name of the requested parameter
 * @returns {string|null|object}
 */
export function psGetRequestParameter(paramName) {
  let vars = {};
  window.location.href.replace(location.hash, '').replace(
    /[?&]+([^=&]+)=?([^&]*)?/gi,
    function (m, key, value) {
      vars[key] = value !== undefined ? value : '';
    }
  );
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
  // we get the refresh flag : on payment step we need to refresh page to be sure
  // amount is correctly updated on payemnt modules
  if (psGetRequestParameter('updatedTransaction') !== null) {
    // this parameter is used to display some info message
    // already set : just refresh page
    window.location.reload();
  } else {
    // not set : add it to the url
    let queryParams = psGetRequestParameter();
    queryParams['updatedTransaction'] = 1;
    const joined = [];
    for (let key in queryParams) {
      let val = queryParams[key]; // gets the value by looking for the key in the object
      joined.push(key + "=" + val);
    }
    window.location.href = window.location.pathname + "?" + joined.join("&");
  }
}
