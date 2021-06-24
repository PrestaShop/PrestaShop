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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
