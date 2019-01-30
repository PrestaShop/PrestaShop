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

export default class EntityFieldsValidator {
  /**
   * Validates entity fields
   *
   * @returns {boolean}
   */
  static validate() {
    $('.js-validation-error').addClass('d-none');

    return this._checkDuplicateSelectedValues() && this._checkRequiredFields();
  }

  /**
   * Checks if there are no duplicate selected values.
   *
   * @returns {boolean}
   * @private
   */
  static _checkDuplicateSelectedValues() {
    const uniqueFields = [];
    let valid = true;

    $('.js-entity-field select').each(function () {
      let value = $(this).val();

      if (value === 'no') {
        return;
      }

      if ($.inArray(value, uniqueFields) !== -1) {
        valid = false;
        $('.js-duplicate-columns-warning').removeClass('d-none');
        return;
      }

      uniqueFields.push(value);
    });

    return valid;
  }

  /**
   * Checks if all required fields are selected.
   *
   * @returns {boolean}
   * @private
   */
  static _checkRequiredFields() {
    let requiredImportFields = $('.js-import-data-table').data('required-fields');

    for (let key in requiredImportFields) {
      if (0 === $(`option[value="${requiredImportFields[key]}"]:selected`).length) {
        $('.js-missing-column-warning').removeClass('d-none');
        $('.js-missing-column').text($(`option[value="${requiredImportFields[key]}"]:first`).text());

        return false;
      }
    }
    return true;
  }
}
