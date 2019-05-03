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

import loginPageMap from "./login-page-map";

const $ = window.$;

/**
 * Class responsible for JS actions in login page.
 */
export default class LoginPage {
  constructor() {
    this._initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    $(document).on('mouseover mouseout', loginPageMap.prestonWinkControl, this._prestonWink);

    $(document).on('click', loginPageMap.forgotPasswordButton, (event) => {
      event.preventDefault();

      $(loginPageMap.flipCard).addClass('flipped');
      $(loginPageMap.loginFormContainer).addClass('d-none');
      $(loginPageMap.forgotFormContainer).removeClass('d-none');
    });

    $(document).on('click', loginPageMap.cancelButton, (event) => {
      event.preventDefault();
      $(loginPageMap.flipCard).removeClass('flipped');
      $(loginPageMap.forgotFormContainer).addClass('d-none');

      setTimeout(() => {
        $(loginPageMap.loginFormContainer).removeClass('d-none');
      }, 200);
    });
  }

  /**
   * Makes preston image wink.
   *
   * @private
   */
  _prestonWink() {
    const $preston = $(loginPageMap.prestonImage);
    const tmpSrc = $preston.attr('src');

    $preston.attr('src', $preston.data('hover-src'));
    $preston.data('hover-src', tmpSrc);
  }
}
