/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ImportMatchConfiguration from './ImportMatchConfiguration';
import ImportDataTable from './ImportDataTable';
import EntityFieldsValidator from './EntityFieldsValidator';
import Importer from './Importer';

export default class ImportDataPage {
  importer: Importer;

  constructor() {
    new ImportMatchConfiguration();
    new ImportDataTable();
    this.importer = new Importer();

    $(document).on('click', '.js-process-import', (e: JQueryEventObject) => this.importHandler(e),
    );
    $(document).on('click', '.js-abort-import', () => this.importer.requestCancelImport(),
    );
    $(document).on('click', '.js-close-modal', () => this.importer.progressModal.hide(),
    );
    $(document).on('click', '.js-continue-import', () => this.importer.continueImport(),
    );
  }

  /**
   * Import process event handler
   */
  importHandler(e: JQueryEventObject): void {
    e.preventDefault();
    const fieldsValidator = new EntityFieldsValidator();

    if (!fieldsValidator.validate()) {
      return;
    }

    const configuration: Record<string, any> = {};

    // Collect the configuration from the form into an array.
    $('.import-data-configuration-form')
      .find(
        '#skip, select[name^=type_value], #csv, #iso_lang, #entity,'
          + '#truncate, #match_ref, #regenerate, #forceIDs, #sendemail,'
          + '#separator, #multiple_value_separator',
      )
      .each((index, $input) => {
        configuration[<string>$($input).attr('name')] = $($input).val();
      });

    this.importer.import(
      $('.js-import-process-button').data('import_url'),
      configuration,
    );
  }
}
