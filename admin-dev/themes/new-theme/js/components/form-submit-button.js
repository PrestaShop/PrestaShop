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
 * Component which allows submitting very simple forms without having to use <form> element.
 *
 * Useful when performing actions on resource where URL contains all needed data.
 * For example, to toggle category status via "POST /categories/2/toggle-status)"
 * or delete cover image via "POST /categories/2/delete-cover-image".
 *
 * Usage example in template:
 *
 * <button class="js-form-submit-btn"
 *         data-form-submit-url="/my-custom-url"          // (required) URL to which form will be submitted
 *         data-form-csrf-token="my-generated-csrf-token" // (optional) to increase security
 *         data-form-confirm-message="Are you sure?"      // (optional) to confirm action before submit
 *         type="button"                                  // make sure its simple button
 *                                                        // so we can avoid submitting actual form
 *                                                        // when our button is defined inside form
 * >
 *     Click me to submit form
 * </button>
 *
 * In page specific JS you have to enable this feature:
 *
 * new FormSubmitButton();
 */
export default class FormSubmitButton {
  constructor() {
    $(document).on('click', '.js-form-submit-btn', function (event) {
      event.preventDefault();

      const $btn = $(this);

      if ($btn.data('form-confirm-message') && false === confirm($btn.data('form-confirm-message'))) {
        return;
      }

      const $form = $('<form>', {
        'action': $btn.data('form-submit-url'),
        'method': 'POST',
      });

      if ($btn.data('form-csrf-token')) {
        $form.append($('<input>', {
          'type': '_hidden',
          'name': '_csrf_token',
          'value': $btn.data('form-csrf-token')
        }));
      }

      $form.appendTo('body').submit();
    });
  }
}
