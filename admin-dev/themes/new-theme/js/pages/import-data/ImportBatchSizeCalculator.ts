/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class ImportBatchSizeCalculator calculates the import batch size.
 * Import batch size is the maximum number of records that
 * the import should handle in one batch.
 */
export default class ImportBatchSizeCalculator {
  targetExecutionTime: number;

  maxAcceleration: number;

  minBatchSize: number;

  maxBatchSize: number;

  importStartTime: number;

  actualExecutionTime: number;

  constructor() {
    // Target execution time in milliseconds.
    this.targetExecutionTime = 5000;

    // Maximum batch size increase multiplier.
    this.maxAcceleration = 4;

    // Minimum and maximum import batch sizes.
    this.minBatchSize = 5;
    this.maxBatchSize = 100;

    this.importStartTime = 0;

    this.actualExecutionTime = 0;
  }

  /**
   * Marks the start of the import operation.
   * Must be executed before starting the import,
   * to be able to calculate the import batch size later on.
   */
  markImportStart(): void {
    this.importStartTime = new Date().getTime();
  }

  /**
   * Marks the end of the import operation.
   * Must be executed after the import operation finishes,
   * to be able to calculate the import batch size later on.
   */
  markImportEnd(): void {
    this.actualExecutionTime = new Date().getTime() - this.importStartTime;
  }

  /**
   * Calculates how much the import execution time can be increased to still be acceptable.
   *
   * @returns {number}
   * @private
   */
  private calculateAcceleration(): number {
    return Math.min(
      this.maxAcceleration,
      this.targetExecutionTime / this.actualExecutionTime,
    );
  }

  /**
   * Calculates the recommended import batch size.
   *
   * @param {number} currentBatchSize current import batch size
   * @param {number} maxBatchSize greater than zero, the batch size that shouldn't be exceeded
   *
   * @returns {number} recommended import batch size
   */
  calculateBatchSize(currentBatchSize: number, maxBatchSize = 0): number {
    if (!this.importStartTime) {
      throw new Error('Import start is not marked.');
    }

    if (!this.actualExecutionTime) {
      throw new Error('Import end is not marked.');
    }

    const candidates = [
      this.maxBatchSize,
      Math.max(
        this.minBatchSize,
        Math.floor(currentBatchSize * this.calculateAcceleration()),
      ),
    ];

    if (maxBatchSize > 0) {
      candidates.push(maxBatchSize);
    }

    return Math.min(...candidates);
  }
}
