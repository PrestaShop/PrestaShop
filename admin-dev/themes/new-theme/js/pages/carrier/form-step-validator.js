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

import {EventEmitter} from '../../components/event-emitter';

const $ = window.$;

/**
 * Responsible for validating form steps
 */
export default class FormStepValidator {
  constructor(formWrapper) {
    this.$formWrapper = $(formWrapper);

    this.handle();

    return {};
  }

  handle() {
    EventEmitter.on('validateFormStep', (e) => {
      e.form.append('step', e.step);
      $.ajax({
        url: this.$formWrapper.data('validate-steps-url'),
        method: 'POST',
        processData: false,
        contentType: false,
        context: this,
        dataType: 'json',
        data: e.form,
      }).then((response) => {
        this.$formWrapper.find('.error-content:not(#error-content)').remove();

        if (response.errors.length === 0) {
          EventEmitter.emit('formStepValidated', e);
        } else {
          this.showFormErrors(response.errors);
        }
      }).catch((error) => {
        showErrorMessage(error.responseJSON.message);
      });
    });
  }

  showFormErrors(errors) {
    for (const field in errors) {
      const errorTemplate = ($('#error-template').get(0).innerHTML)
        .replace(/id="error-content"/, '')
        .replace(/__MESSAGE__/, errors[field]);
      if (!document.querySelector(`.${field} .error-content`)) {
        document.querySelector(`.${field}`).firstElementChild.lastElementChild
          .insertAdjacentHTML('beforeend', errorTemplate);
      }
    }
  }
}
