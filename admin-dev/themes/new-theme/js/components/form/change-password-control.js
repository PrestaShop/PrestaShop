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

import ChangePasswordHandler from "../change-password-handler";

const $ = window.$;

/**
 * Class responsible for actions related to "change password" form type.
 */
export default class ChangePasswordControl {
  constructor() {
    this.$inputsBlock = $('.js-change-password-block');
    this.showButtonSelector = '.js-change-password';
    this.hideButtonSelector = '.js-change-password-cancel';
    this.generatePasswordButtonSelector = '#employee_change_password_generate_password_button';
    this.$newPasswordInputs = this.$inputsBlock.find(
      '#employee_change_password_new_password_first,' +
      '#employee_change_password_new_password_second,' +
      '#employee_change_password_generated_password'
    );
    this.$submittableInputs = this.$inputsBlock.find(
      '#employee_change_password_old_password,' +
      '#employee_change_password_new_password_first,' +
      '#employee_change_password_new_password_second'
    );

    this.passwordHandler = new ChangePasswordHandler();
    this.initEvents();
  }

  /**
   * Initialize events.
   */
  initEvents() {
    $(document).on('click', this.showButtonSelector, (e) => {
      this._hide($(e.currentTarget));
      this._showInputsBlock();
    });

    $(document).on('click', this.hideButtonSelector, () => {
      this._hideInputsBlock();
      this._show($(this.showButtonSelector));
    });

    $(document).on('click', this.generatePasswordButtonSelector, () => {
      this.passwordHandler.generatePassword(this.$newPasswordInputs);
    });

    this.passwordHandler.watchPasswordStrength(this.$newPasswordInputs);
  }

  /**
   * Show the password inputs block.
   */
  _showInputsBlock() {
    this._show(this.$inputsBlock);
    this.$submittableInputs.removeAttr('disabled');
  }

  /**
   * Hide the password inputs block.
   */
  _hideInputsBlock() {
    this._hide(this.$inputsBlock);
    this.$submittableInputs.attr('disabled', 'disabled');
    this.$inputsBlock.find('input').val('');
    this.$inputsBlock.find('.form-text').text('');
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
