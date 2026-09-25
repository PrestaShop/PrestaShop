/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {
  describeDuplicateImportColumns,
  ImportColumnSelection,
} from '@app/utils/duplicate-import-columns';

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
    const selections: ImportColumnSelection[] = [];

    $('.js-entity-field select').each(function (index: number) {
      const value = $(this).val();

      if (value === undefined || value === 'no') {
        return;
      }

      selections.push({
        // The merchant counts the columns of the file from one, left to right.
        column: index + 1,
        value: String(value),
        label: $(this).find('option:selected').text().trim(),
      });
    });

    const duplicates = describeDuplicateImportColumns(selections);

    if (duplicates === '') {
      return true;
    }

    $('.js-duplicate-columns-warning').removeClass('d-none');
    $('.js-duplicate-columns').text(duplicates);

    return false;
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
