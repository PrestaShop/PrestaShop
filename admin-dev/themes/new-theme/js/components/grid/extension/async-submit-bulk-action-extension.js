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

import ConfirmModal from '../../modal';

const {$} = window;

export default class AsyncSubmitBulkActionExtension {

  constructor({ successCallback = null, errorCallback = null, } = {}) {
    this.successCallback = this.setSuccessCallback(successCallback);
    this.errorCallback = this.setErrorCallback(errorCallback);

    return {
      extend: (grid) => this.extend(grid),
    };
  }

   setSuccessCallback(callback) {
    return this.isCallback(callback) ? callback :
      (response) => {
        window.showSuccessMessage(response.message);
        window.location.reload();
      }
  }

  setErrorCallback(callback) {
    return this.isCallback(callback) ? callback :
      (response) => {
        window.showErrorMessage(response.message);
        window.location.reload();
      }
  }

  isCallback(callback) {
    return callback && typeof callback === "function";
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-bulk-action-submit-async-btn', (event) => {
      this.submit(event, grid);
    });
  }

  /**
   * Collects required data and submits the form.
   * @param event
   * @param grid
   */
  submit(event, grid) {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');
    const confirmTitle = $submitBtn.data('confirmTitle');
    const hasConfirmTitle = confirmTitle !== undefined;
    const hasConfirmationMessage = confirmMessage !== undefined && confirmMessage.length > 0;
    const hasAllConfirmationMessages = hasConfirmationMessage && hasConfirmTitle;

    if (hasAllConfirmationMessages) {
      this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
    } else if (hasConfirmationMessage && confirm(confirmMessage)) {
      this.submitForm($submitBtn, grid);
    } else {
      this.submitForm($submitBtn, grid);
    }
  }

  submitForm($submitBtn, grid) {
    const $form = $(`#${grid.getId()}_filter_form`);
    const $checkedInputs = this.getCheckedInputs($form);
    const ids = this.getCheckedIds($checkedInputs);

    const inputName = this.getInputName($checkedInputs);
    const chunkSize = parseInt($submitBtn.data('chunkSize'), 10);
    const chunkedIds = this.chunkArray(ids, chunkSize);
    const submitMethod = $submitBtn.data('form-method');
    const formUrl = $submitBtn.data('form-url');

    if (!['POST', 'GET', 'DELETE', 'PUT'].includes(submitMethod)) {
      return;
    }

    /**
     * Async submit action which repeats itself in recursive order.
     * @param items
     * @param successCallback
     * @param errorCallback
     */
    const asyncSubmit = (items, successCallback, errorCallback) => {
      if (items.length === 0) {
        return;
      }

      const firstIdsChunk = items.shift();
      const data = {};
      data[inputName] = firstIdsChunk;

      $.ajax({
        type: submitMethod,
        url: formUrl,
        data,
        dataType: 'json',
      }).then((response) => {
        if (!response.success) {
          errorCallback(response);
        }

        if (response.success && items.length > 0) {
          asyncSubmit(items, successCallback, errorCallback);
        } else {
          successCallback(response);
        }
      }).catch((error) => {
        const response = error.responseJSON;
        errorCallback(response);
      });
    };

    asyncSubmit(chunkedIds, this.successCallback, this.errorCallback);
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   * @param {string} confirmMessage
   * @param {string} confirmTitle
   */
  showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle) {
    const confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
    const closeButtonLabel = $submitBtn.data('closeButtonLabel');
    const confirmButtonClass = $submitBtn.data('confirmButtonClass');

    const modal = new ConfirmModal({
      id: `${grid.getId()}_grid_confirm_modal`,
      confirmTitle,
      confirmMessage,
      confirmButtonLabel,
      closeButtonLabel,
      confirmButtonClass,
    }, () => this.submitForm($submitBtn, grid));

    modal.show();
  }

  /**
   * Gets the only checked inputs.
   * @param $form
   * @return {*}
   */
  getCheckedInputs($form) {
    return $form.find('.js-bulk-action-checkbox:checked');
  }

  /**
   * From checked inputs it returns input values
   * @param $inputs
   * @return {*}
   */
  getCheckedIds($inputs) {
    return $inputs.map((index, el) => parseInt($(el).val(), 10));
  }

  /**
   * Since bulk action has same name we can return single name from all.
   * @param $inputs
   * @return {*}
   */
  getInputName($inputs) {
    return $inputs.attr('name').replace('[]', '');
  }

  /**
   * Divides results by chunk size.
   * @param array
   * @param chunkSize
   * @return {Array}
   */
  chunkArray(array, chunkSize) {
    const results = [];
    while (array.length) {
      results.push(array.splice(0, chunkSize));
    }

    return results;
  }
}
