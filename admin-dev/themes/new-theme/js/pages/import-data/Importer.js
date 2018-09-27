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

export default class Importer {
  import(importConfiguration) {
    this.configuration = importConfiguration;
    this.progressModal = new ImportProgressModal;

    this.progressModal.reset();
    this.progressModal.show();

    // Starting the import for 5 elements.
    this._ajaxImport(0, 5, true);
  }

  _ajaxImport(offset, limit, validateOnly = false, nextStepIndex = 0, recurringVariables = {}) {
    this._mergeConfiguration({
      ajax: 1,
      action: 'import',
      tab: 'AdminImport',
      token: token,
      offset: offset,
      limit: limit,
      validateOnly: validateOnly ? 1 : 0,
      moreStep: nextStepIndex,
      crossStepsVars: JSON.stringify(recurringVariables)
    });

    // Start time of current import step
    let startingTime = new Date().getTime();

    $.post({
      url: 'index.php',
      dataType: 'json',
      data: this.configuration,
      success: (response) => {
        // Update import progress
        this.progressModal.updateProgress(response.doneCount / response.totalCount * 100);

        if (response.informations) {
          this.progressModal.showInfoMessages(response.informations);
        }

        if (response.warnings) {
          this.progressModal.showWarningMessages(response.warnings);
        }

        if (response.errors) {
          this.progressModal.showErrorMessages(response.errors);

          // If there are errors and it's not validation step - stop the import.
          if (!validateOnly) {
            return false;
          }
        }

        if (!response.isFinished) {
          let timeTaken = new Date().getTime() - startingTime;
          //@todo finish
        }
      }
    });
  }

  /**
   * Set the import configuration.
   *
   * @param importConfiguration
   */
  set configuration(importConfiguration) {
    this.importConfiguration = importConfiguration;
  }

  /**
   * Get the import configuration.
   *
   * @returns {*}
   */
  get configuration() {
    return this.importConfiguration;
  }

  /**
   * Set progress modal.
   *
   * @param {ImportProgressModal} modal
   */
  set progressModal(modal) {
    this.modal = modal;
  }

  /**
   * Get progress modal.
   *
   * @returns {ImportProgressModal}
   */
  get progressModal() {
    return this.modal;
  }

  /**
   * Get the target processing time for one import step.
   *
   * @returns {number}
   */
  get targetTime() {
    return 5;
  }

  /**
   * Merge given configuration into current import configuration.
   *
   * @param {Object} configuration
   * @private
   */
  _mergeConfiguration(configuration) {
    for (let key in configuration) {
      this.importConfiguration.push({
        name: key,
        value: configuration[key]
      });
    }
  }
}
