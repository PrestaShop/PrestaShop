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

const $ = window.$;

/**
 * Responsible for connecting to addons marketplace.
 */
export default class AddonsConnector {
  constructor(
    addonsConnectFormSelector,
    addonsLoginButtonSelector
  ) {
    this.addonsConnectFormSelector = addonsConnectFormSelector;
    this.$addonsLoginButton = $(addonsLoginButtonSelector);

    this._initEvents();

    return {};
  }

  /**
   * Initialize events related to connection to addons.
   *
   * @private
   */
  _initEvents() {
    $('body').on('submit', this.addonsConnectFormSelector, (event) => {
      const $form = $(event.currentTarget);
      event.preventDefault();
      event.stopPropagation();

      this._connect($form.attr('action'), $form.serialize());
    });
  }

  /**
   * Do a POST request to connect to addons.
   *
   * @param {String} addonsConnectUrl
   * @param {Object} formData
   *
   * @private
   */
  _connect(addonsConnectUrl, formData) {
    $.ajax({
      method: 'POST',
      url: addonsConnectUrl,
      dataType: 'json',
      data: formData,
      beforeSend: () => {
        this.$addonsLoginButton.show();
        $('button.btn[type="submit"]', this.addonsConnectFormSelector).hide();
      }
    }).then((response) => {
      if (response.success === 1) {
        location.reload();
      } else {
        $.growl.error({message: response.message});
        this.$addonsLoginButton.hide();
        $('button.btn[type="submit"]', this.addonsConnectFormSelector).fadeIn();
      }
    });
  }
}
