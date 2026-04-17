/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Get the correct transition keyword of the browser.
 * @param {string} type - The property name (transition for example).
 * @param {string} lifecycle - Which lifecycle of the property name to catch (end, start...).
 * @return {string} The transition keywoard of the browser.
*/

// eslint-disable-next-line
function getAnimationEvent(type, lifecycle) {
  const el = document.createElement('element');
  const typeUpper = type.charAt(0).toUpperCase() + type.substring(1);
  const lifecycleUpper = lifecycle.charAt(0).toUpperCase() + lifecycle.substring(1);

  const properties = {
    transition: `${type}${lifecycle}`,
    OTransition: `o${typeUpper}${lifecycleUpper}`,
    MozTransition: `${type}${lifecycle}`,
    WebkitTransition: `webkit${typeUpper}${lifecycleUpper}`,
  };

  const key = Object.keys(properties).find((propKey) => el.style[propKey] !== undefined);

  return key !== undefined ? properties[key] : false;
}
