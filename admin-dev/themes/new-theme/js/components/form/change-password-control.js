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
 * Class responsible for actions related to "change password" form type.
 */
export default class ChangePasswordControl {
  constructor() {
    this.inputsBlock = $('.js-change-password-block');
    this.showButtonSelector = '.js-change-password';
    this.hideButtonSelector = '.js-change-password-cancel';
    this.submittableInputs = this.inputsBlock.find(
      '#employee_change_password_old_password,' +
      '#employee_change_password_new_password_first,' +
      '#employee_change_password_new_password_second'
    );

    this.initEvents();
  }

  /**
   * Initialize events.
   */
  initEvents() {
    const t = this;
    $(document).on('click', this.showButtonSelector, function () {
      t._hide($(this));
      t._showInputsBlock();
    });

    $(document).on('click', this.hideButtonSelector, function () {
      t._hideInputsBlock();
      t._show($(t.showButtonSelector));
    });
  }

  /**
   * Show the password inputs block.
   */
  _showInputsBlock() {
    this._show(this.inputsBlock);
    this.submittableInputs.removeAttr('disabled');
  }

  /**
   * Hide the password inputs block.
   */
  _hideInputsBlock() {
    this._hide(this.inputsBlock);
    this.submittableInputs.attr('disabled', 'disabled');
  }

  /**
   * Hide an element.
   */
  _hide($el) {
    $el.addClass('d-none');
  }

  /**
   * Show hidden element.
   */
  _show($el) {
    $el.removeClass('d-none');
  }
}
