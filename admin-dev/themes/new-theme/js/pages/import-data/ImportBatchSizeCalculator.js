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

/**
 * Class ImportBatchSizeCalculator calculates the import batch size.
 * Import batch size is the maximum number of records that
 * the import should handle in one batch.
 */
export default class ImportBatchSizeCalculator {
  constructor() {
    // Target execution time in milliseconds.
    this._targetExecutionTime = 5000;

    // Maximum batch size increase multiplier.
    this._maxAcceleration = 4;

    // Minimum and maximum import batch sizes.
    this._minBatchSize = 5;
    this._maxBatchSize = 100;
  }

  /**
   * Marks the start of the import operation.
   * Must be executed before starting the import,
   * to be able to calculate the import batch size later on.
   */
  markImportStart() {
    this._importStartTime = new Date().getTime();
  }

  /**
   * Marks the end of the import operation.
   * Must be executed after the import operation finishes,
   * to be able to calculate the import batch size later on.
   */
  markImportEnd() {
    this._actualExecutionTime = new Date().getTime() - this._importStartTime;
  }

  /**
   * Calculates how much the import execution time can be increased to still be acceptable.
   *
   * @returns {number}
   * @private
   */
  _calculateAcceleration() {
    return Math.min(this._maxAcceleration, this._targetExecutionTime / this._actualExecutionTime);
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
    if (!this._importStartTime) {
      throw 'Import start is not marked.';
    }

    if (!this._actualExecutionTime) {
      throw 'Import end is not marked.';
    }

    let candidates = [
      this._maxBatchSize,
      Math.max(this._minBatchSize, Math.floor(currentBatchSize * this._calculateAcceleration()))
    ];

    if (maxBatchSize > 0) {
      candidates.push(maxBatchSize);
    }

    return Math.min(...candidates);
  }
}
