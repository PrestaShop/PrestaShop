/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Assert that value is undefined
 *
 * @param value
 */
export function isUndefined(value: any): value is undefined {
  return typeof value === 'undefined';
}

/**
 * Assert that input exist is an HTMLInputElement and if so returns its checked status
 *
 * @param input
 */
export function isChecked(input: any): boolean {
  return input instanceof HTMLInputElement && input.checked;
}
