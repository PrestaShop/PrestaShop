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

export default class AsyncSubmitBulkActionExtension {
  constructor() {
    return {
      extend: grid => this.extend(grid),
    };
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

  submit(event, grid) {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');

    if (typeof confirmMessage !== 'undefined' && confirmMessage.length > 0 && !confirm(confirmMessage)) {
      return;
    }

    const $form = $(`#${grid.getId()}_filter_form`);
    const $checkedInputs = this.getCheckedInputs($form);
    const ids = this.getCheckedIds($checkedInputs);

    const inputName = this.getInputName($checkedInputs);
    const chunkSize = parseInt($submitBtn.data('step'), 10);
    const chunkedIds = this.chunkArray(ids, chunkSize);
    const submitMethod = $submitBtn.data('form-method');
    const formUrl = $submitBtn.data('form-url');

    if (!['POST', 'GET', 'DELETE', 'PUT'].includes(submitMethod)) {
      return;
    }

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

    asyncSubmit(
      chunkedIds,
      (response) => {
        showSuccessMessage(response.message);
        window.location.reload();
      },
      (response) => {
        showErrorMessage(response.message);
        window.location.reload();
      });
  }

  getCheckedInputs($form) {
    return $form.find('.js-bulk-action-checkbox:checked');
  }

  getCheckedIds($inputs) {
    return $inputs.map((index, el) => parseInt($(el).val(), 10));
  }

  getInputName($inputs) {
    return $inputs.attr('name').replace('[]', '');
  }

  chunkArray(array, chunkSize) {
    const results = [];
    while (array.length) {
      results.push(array.splice(0, chunkSize));
    }

    return results;
  }
}
