/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default class PostSizeChecker {
  postSizeLimitThreshold: number;

  constructor() {
    // How close can we get to the post size limit. 0.9 means 90%.
    this.postSizeLimitThreshold = 0.9;
  }

  /**
   * Check if given postSizeLimit is reaching the required post size
   *
   * @param {number} postSizeLimit
   * @param {number} requiredPostSize
   *
   * @returns {boolean}
   */
  isReachingPostSizeLimit(
    postSizeLimit: number,
    requiredPostSize: number,
  ): boolean {
    return requiredPostSize >= postSizeLimit * this.postSizeLimitThreshold;
  }

  /**
   * Get required post size in megabytes.
   *
   * @param {number} requiredPostSize
   *
   * @returns {number}
   */
  getRequiredPostSizeInMegabytes(requiredPostSize: number): number {
    const requiredSize = requiredPostSize / (1024 * 1024);

    return parseInt(<string>(<unknown>requiredSize), 10);
  }
}
