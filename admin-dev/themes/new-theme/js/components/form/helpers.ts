/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Used to display errors fetched from PrestaShop API in a JSON format.
 * The expected format looks like this:
 * {
 *   errors: {
 *     price: 'Invalid negative value',
 *     name: 'Forbidden blank value',
 *   },
 * }
 *
 * @param jsonResponse
 */
export function notifyFormErrors(jsonResponse: any): void {
  Object.keys(jsonResponse.errors).forEach((field: string) => {
    if (Object.prototype.hasOwnProperty.call(jsonResponse.errors, field)) {
      let fieldErrors: string[];

      if (Array.isArray(jsonResponse.errors[field])) {
        fieldErrors = jsonResponse.errors[field];
      } else {
        fieldErrors = [jsonResponse.errors[field]];
      }

      fieldErrors.forEach((error: string) => {
        $.growl.error({message: `${field}: ${error}`});
      });
    }
  });
};

export default {
  notifyFormErrors,
};
