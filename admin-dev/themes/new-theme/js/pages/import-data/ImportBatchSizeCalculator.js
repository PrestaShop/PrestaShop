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

/**
 * Class ImportBatchSizeCalculator calculates the import batch size.
 * Import batch size is the maximum number of records that
 * the import should handle in one batch.
 */
export default class ImportBatchSizeCalculator {
  constructor() {
    // Target execution time in milliseconds.
    this.targetExecutionTime = 5000;

    // Maximum batch size increase multiplier.
    this.maxAcceleration = 4;

    // Minimum and maximum import batch sizes.
    this.minBatchSize = 5;
    this.maxBatchSize = 100;
  }

  /**
   * Marks the start of the import operation.
   * Must be executed before starting the import,
   * to be able to calculate the import batch size later on.
   */
  markImportStart() {
    this.importStartTime = new Date().getTime();
  }

  /**
   * Marks the end of the import operation.
   * Must be executed after the import operation finishes,
   * to be able to calculate the import batch size later on.
   */
  markImportEnd() {
    this.actualExecutionTime = new Date().getTime() - this.importStartTime;
  }

  /**
   * Calculates how much the import execution time can be increased to still be acceptable.
   *
   * @returns {number}
   * @private
   */
  calculateAcceleration() {
    return Math.min(this.maxAcceleration, this.targetExecutionTime / this.actualExecutionTime);
  }

  /**
   * Calculates the recommended import batch size.
   *
   * @param {number} currentBatchSize current import batch size
   * @param {number} maxBatchSize greater than zero, the batch size that shouldn't be exceeded
   *
   * @returns {number} recommended import batch size
   */
  calculateBatchSize(currentBatchSize, maxBatchSize = 0) {
    if (!this.importStartTime) {
      throw new Error('Import start is not marked.');
    }

    if (!this.actualExecutionTime) {
      throw new Error('Import end is not marked.');
    }

    const candidates = [
      this.maxBatchSize,
      Math.max(this.minBatchSize, Math.floor(currentBatchSize * this.calculateAcceleration())),
    ];

    if (maxBatchSize > 0) {
      candidates.push(maxBatchSize);
    }

    return Math.min(...candidates);
  }
}
