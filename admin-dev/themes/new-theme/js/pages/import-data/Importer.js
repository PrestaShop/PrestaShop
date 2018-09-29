/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import ImportProgressModal from './ImportProgressModal';
import ImportBatchSizeCalculator from './ImportBatchSizeCalculator';

export default class Importer {
  constructor() {
    this.progressModal = new ImportProgressModal;
    this.batchSizeCalculator = new ImportBatchSizeCalculator;

    // Default number of rows in one batch of the import.
    this.defaultBatchSize = 5;
  }

  /**
   * Process the import.
   *
   * @param importConfiguration serialized import data configuration form.
   */
  import(importConfiguration) {
    this.configuration = importConfiguration;

    // Total number of rows to be imported.
    this.totalRowsCount = 0;

    // Flags that mark that there were warnings or errors during import.
    this.hasWarnings = false;
    this.hasErrors = false;

    // Resetting the import progress modal and showing it.
    this.progressModal.reset();
    this.progressModal.show();

    // Starting the import with 5 elements in batch.
    this._ajaxImport(0, this.defaultBatchSize);
  }

  /**
   * Process the ajax import request.
   *
   * @param {number} offset row number, from which the import job will start processing data.
   * @param {number} batchSize batch size of this import job.
   * @param {boolean} validateOnly whether the data should be only validated, if false - the data will be imported.
   * @param {number} stepIndex current step index, retrieved from the ajax response
   * @param {Object} recurringVariables variables which are recurring between import batch jobs.
   * @private
   */
  _ajaxImport(offset, batchSize, validateOnly = true, stepIndex = 0, recurringVariables = {}) {
    this._mergeConfiguration({
      ajax: 1,
      action: 'import',
      tab: 'AdminImport',
      token: token,
      offset: offset,
      limit: batchSize,
      validateOnly: validateOnly ? 1 : 0,
      moreStep: stepIndex,
      crossStepsVars: JSON.stringify(recurringVariables)
    });

    // Marking the start of import operation.
    this.batchSizeCalculator.markImportStart();
    this.progressModal.showAbortImportButton();

    $.post({
      url: 'index.php',
      dataType: 'json',
      data: this.configuration,
      success: (response) => {
        if (this._importCancelRequested) {
          this._cancelImport();
          return false;
        }

        let nextStepIndex = response.oneMoreStep !== undefined ? response.oneMoreStep : stepIndex;

        if (response.totalCount !== undefined) {
          // The total rows count is retrieved only in the first batch response.
          this.totalRowsCount = response.totalCount;
        }

        // Update import progress
        this.progressModal.updateProgress(response.doneCount, this.totalRowsCount);

        if (!validateOnly) {
          // Set the progress label to "Importing"
          this.progressModal.setImportingProgressLabel();
        }

        // Information messages are not shown during validation.
        if (!validateOnly && response.informations) {
          this.progressModal.showInfoMessages(response.informations);
        }

        if (response.errors) {
          this.hasErrors = true;
          this.progressModal.showErrorMessages(response.errors);

          // If there are errors and it's not validation step - stop the import.
          // If it's validation step - we will show all errors once it finishes.
          if (!validateOnly) {
            this._onImportStop();
            return false;
          }
        } else if (response.warnings) {
          this.hasWarnings = true;
          this.progressModal.showWarningMessages(response.warnings);
        }

        if (!response.isFinished) {
          // Marking the end of import operation.
          this.batchSizeCalculator.markImportEnd();

          // Calculate next import batch size and offset.
          let nextBatchSize = this.batchSizeCalculator.calculateBatchSize(batchSize);
          let nextOffset = offset + nextBatchSize;

          // Run the import again for the next batch.
          return this._ajaxImport(
            nextOffset,
            nextBatchSize,
            validateOnly,
            nextStepIndex,
            response.crossStepsVariables
          );
        }

        // All import batches are finished successfully.
        // If it was only validating the import data until this point,
        // we have to run the data import now.
        if (validateOnly) {
          // If errors occurred during validation - stop the import.
          if (this.hasErrors) {
            this._onImportStop();
            return false;
          }

          if (this.hasWarnings) {
            // Show the button to ignore warnings.
            this.progressModal.showContinueImportButton();
            this._onImportStop();
            return false;
          } else {
            // Reset the progress bar to 0
            this.progressModal.updateProgress(this.totalRowsCount, this.totalRowsCount);

            // Continue with the data import.
            return this._ajaxImport(0, this.defaultBatchSize, false);
          }
        } else if (stepIndex < nextStepIndex) {
          // If it's still not the last step of the import - continue with the next step.
          return this._ajaxImport(
            0,
            this.defaultBatchSize,
            false,
            nextStepIndex,
            response.crossStepsVariables
          );
        }

        // Import is completely finished.
        this._onImportFinish();
      },
      error: (XMLHttpRequest, textStatus) => {
        if (textStatus === 'parsererror') {
          textStatus = 'Technical error: Unexpected response returned by server. Import stopped.';
        }

        this._onImportStop();
        this.progressModal.showErrorMessages([textStatus]);
      }
    });
  }

  /**
   * Continue the import when it was stopped.
   */
  continueImport() {
    if (!this.configuration) {
      throw 'Missing import configuration. Make sure the import had started before continuing.';
    }

    this.progressModal.hideContinueImportButton();
    this.progressModal.clearWarningMessages();
    this._ajaxImport(0, this.defaultBatchSize, false);
  }

  /**
   * Set the import configuration.
   *
   * @param importConfiguration
   */
  set configuration(importConfiguration) {
    this._importConfiguration = importConfiguration;
  }

  /**
   * Get the import configuration.
   *
   * @returns {*}
   */
  get configuration() {
    return this._importConfiguration;
  }

  /**
   * Set progress modal.
   *
   * @param {ImportProgressModal} modal
   */
  set progressModal(modal) {
    this._modal = modal;
  }

  /**
   * Get progress modal.
   *
   * @returns {ImportProgressModal}
   */
  get progressModal() {
    return this._modal;
  }

  /**
   * Request import cancellation.
   * Import operation will be cancelled at next iteration when requested.
   */
  requestCancelImport() {
    this._importCancelRequested = true;
  }

  /**
   * Merge given configuration into current import configuration.
   *
   * @param {Object} configuration
   * @private
   */
  _mergeConfiguration(configuration) {
    for (let key in configuration) {
      this._importConfiguration.push({
        name: key,
        value: configuration[key]
      });
    }
  }

  /**
   * Cancel the import process.
   * @private
   */
  _cancelImport() {
    this.progressModal.hide();
    this._importCancelRequested = false;
  }

  /**
   * Executed when import is stopped - hides/shows appropriate buttons.
   * @private
   */
  _onImportStop() {
    this.progressModal.showCloseModalButton();
    this.progressModal.hideAbortImportButton();
  }

  /**
   * Additional actions when import is finished.
   * @private
   */
  _onImportFinish() {
    this._onImportStop();
    this.progressModal.showSuccessMessage();
    this.progressModal.setImportedProgressLabel();
    this.progressModal.updateProgress(this.totalRowsCount, this.totalRowsCount);
  }
}
