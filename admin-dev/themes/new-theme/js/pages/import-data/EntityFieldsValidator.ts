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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

const {$} = window;

export default class EntityFieldsValidator {
  /**
   * Validates entity fields
   *
   * @returns {boolean}
   */
  validate(): boolean {
    $('.js-validation-error').addClass('d-none');

    return this.checkDuplicateSelectedValues() && this.checkRequiredFields();
  }

  /**
   * Checks if there are no duplicate selected values.
   *
   * @returns {boolean}
   * @private
   */
  checkDuplicateSelectedValues(): boolean {
    const uniqueFields: Array<string | number | string[] | undefined> = [];
    let valid = true;

    $('.js-entity-field select').each(function () {
      const value = $(this).val();

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
  private checkRequiredFields(): boolean {
    const requiredImportFields = $('.js-import-data-table').data(
      'required-fields',
    );

    /* eslint-disable-next-line */
    for (const key in requiredImportFields) {
      if (
        $(`option[value="${requiredImportFields[key]}"]:selected`).length === 0
      ) {
        $('.js-missing-column-warning').removeClass('d-none');
        $('.js-missing-column').text(
          $(`option[value="${requiredImportFields[key]}"]:first`).text(),
        );

        return false;
      }
    }

    return true;
  }
}
