/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Encapsulates selectors for multi store restriction component
 */
export default {
  multiStoreRestrictionCheckbox: '.js-multi-store-restriction-checkbox',
  multiStoreRestrictionSwitch: '.js-multi-store-restriction-switch',
  sourceField: (targetValue: string): string => `[data-shop-restriction-source="${targetValue}"]`,
};
