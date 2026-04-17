/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
