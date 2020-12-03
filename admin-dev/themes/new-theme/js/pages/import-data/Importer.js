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

import ImportProgressModal from './ImportProgressModal';
import ImportBatchSizeCalculator from './ImportBatchSizeCalculator';
import PostSizeChecker from './PostSizeChecker';

export default class Importer {
  constructor() {
    this.configuration = {};
    this.progressModal = new ImportProgressModal;
    this.batchSizeCalculator = new ImportBatchSizeCalculator;
    this.postSizeChecker = new PostSizeChecker();

    // Default number of rows in one batch of the import.
    this.defaultBatchSize = 5;
  }

  /**
   * Process the import.
   *
   * @param {String} importUrl url of the controller, processing the import.
   * @param {Object} configuration import configuration.
   */
  import(importUrl, configuration) {
    this._mergeConfiguration(configuration);
    this.importUrl = importUrl;

    // Total number of rows to be imported.
    this.totalRowsCount = 0;

    // Flags that mark that there were warnings or errors during import.
    this.hasWarnings = false;
    this.hasErrors = false;

    // Resetting the import progress modal and showing it.
    this.progressModal.reset();
    this.progressModal.show();

    // Starting the import with default batch size, which is adjusted for next iterations.
    this._ajaxImport(0, this.defaultBatchSize);
  }

  /**
   * Process the ajax import request.
   *
   * @param {number} offset row number, from which the import job will start processing data.
   * @param {number} batchSize batch size of this import job.
   * @param {boolean} validateOnly whether the data should be only validated, if false - the data will be imported.
   * @param {Object} recurringVariables variables which are recurring between import batch jobs.
   * @private
   */
  _ajaxImport(offset, batchSize, validateOnly = true, recurringVariables = {}) {
    this._mergeConfiguration({
      offset: offset,
      limit: batchSize,
      validateOnly: validateOnly ? 1 : 0,
      crossStepsVars: JSON.stringify(recurringVariables)
    });

    this._onImportStart();

    $.post({
      url: this.importUrl,
      dataType: 'json',
      data: this.configuration,
      success: (response) => {
        if (this._importCancelRequested) {
          this._cancelImport();
          return false;
        }

        let hasErrors = response.errors && response.errors.length,
            hasWarnings = response.warnings && response.warnings.length,
            hasNotices = response.notices && response.notices.length;

        if (response.totalCount !== undefined && response.totalCount) {
          // The total rows count is retrieved only in the first batch response.
          this.totalRowsCount = response.totalCount;
        }

        // Update import progress.
        this.progressModal.updateProgress(response.doneCount, this.totalRowsCount);

        if (!validateOnly) {
          // Set the progress label to "Importing".
          this.progressModal.setImportingProgressLabel();
        }

        // Information messages are not shown during validation.
        if (!validateOnly && hasNotices) {
          this.progressModal.showInfoMessages(response.notices);
        }

        if (hasErrors) {
          this.hasErrors = true;
          this.progressModal.showErrorMessages(response.errors);

          // If there are errors and it's not validation step - stop the import.
          // If it's validation step - we will show all errors once it finishes.
          if (!validateOnly) {
            this._onImportStop();
            return false;
          }
        } else if (hasWarnings) {
          this.hasWarnings = true;
          this.progressModal.showWarningMessages(response.warnings);
        }

        if (!response.isFinished) {
          // Marking the end of import operation.
          this.batchSizeCalculator.markImportEnd();

          // Calculate next import batch size and offset.
          let nextOffset = offset + batchSize;
          let nextBatchSize = this.batchSizeCalculator.calculateBatchSize(batchSize, this.totalRowsCount);

          // Showing a warning if post size limit is about to be reached.
          if (this.postSizeChecker.isReachingPostSizeLimit(response.postSizeLimit, response.nextPostSize)) {
            this.progressModal.showPostLimitMessage(
              this.postSizeChecker.getRequiredPostSizeInMegabytes(response.nextPostSize)
            );
          }

          // Run the import again for the next batch.
          return this._ajaxImport(
            nextOffset,
            nextBatchSize,
            validateOnly,
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
          }

          // Update the progress bar to 100%.
          this.progressModal.updateProgress(this.totalRowsCount, this.totalRowsCount);

          // Continue with the data import.
          return this._ajaxImport(0, this.defaultBatchSize, false);
        }

        // Import is completely finished.
        this._onImportFinish();
      },
      error: (XMLHttpRequest, textStatus, errorCode) => {
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
    this.progressModal.hideCloseModalButton();
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
      this._importConfiguration[key] = configuration[key];
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
   * Additional actions when import is stopped.
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

  /**
   * Additional actions when import is starting.
   * @private
   */
  _onImportStart() {
    // Marking the start of import operation.
    this.batchSizeCalculator.markImportStart();
    this.progressModal.showAbortImportButton();
  }
}
