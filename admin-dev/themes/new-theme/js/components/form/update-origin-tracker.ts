/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Remembers which field an update cycle started from, so that values derived from it are never
 * written back onto it.
 *
 * Assigning a mapped value re-enters the same update handler, so a pair of fields that convert
 * into each other echoes: `12` typed as a tax included price becomes `9.756098` tax excluded at
 * the form's precision, and converting that back gives `12.000001`, which replaced what had just
 * been typed. The conversion is lossy in both directions, so the field being edited has to win.
 */
export default class UpdateOriginTracker {
  private origin: string | null = null;

  /**
   * Runs an update cycle for a field. Cycles started while one is already running belong to it,
   * so a chain of derived updates keeps the field the user actually edited as its origin.
   */
  run(modelKey: string, update: () => void): void {
    const isOutermostUpdate = this.origin === null;

    if (isOutermostUpdate) {
      this.origin = modelKey;
    }

    try {
      update();
    } finally {
      if (isOutermostUpdate) {
        this.origin = null;
      }
    }
  }

  /**
   * @returns {boolean} whether this field is the one the running update cycle started from
   */
  isOrigin(modelKey: string): boolean {
    return this.origin === modelKey;
  }
}
