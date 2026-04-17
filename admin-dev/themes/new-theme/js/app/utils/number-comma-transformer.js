/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const findAllUnwantedCharsExceptTheLatestOne = /(?:(?!^-\d+))[^\d]+(?=.*[^\d])/g;
const findAllUnwantedChars = /(?:(?!^-\d+))([^\d]+)/g;

/**
 * If there is a dot in the string
 * split the string at the first dot, and
 * replace all unwanted characters.
 * Otherwise, replace all unwanted characters expect the
 * latest one, and replace the latest character
 * by a dot.
 */
export const transform = (value) => {
  let val = value;
  const unwantedChars = val.match(findAllUnwantedChars);

  if (unwantedChars === null) {
    return val;
  }

  if (unwantedChars.length > 1) {
    const unwantedCharsSet = new Set(unwantedChars);
    const unique = Array.from(unwantedCharsSet);

    if (unique.length === 1) {
      return val.replace(findAllUnwantedChars, '');
    }
  }

  val = val
    .replace(findAllUnwantedCharsExceptTheLatestOne, '')
    .replace(findAllUnwantedChars, '.');

  return val;
};

const clearNumberInputValue = (event, selector) => {
  if (!event.target.matches(selector)) {
    return;
  }

  const {value} = event.target;
  event.target.value = transform(value);
};

export default (selector) => {
  document.addEventListener(
    'change',
    (event) => {
      clearNumberInputValue(event, selector);
    },
    true,
  );
};
