/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Checks if correct addresses are selected.
 * There is a case when options list cannot contain cart addresses 'selected' values
 *  because those are outdated in db (e.g. deleted after cart creation or country is disabled)
 *
 * @param {Array} addresses
 *
 * @returns {boolean}
 */
export const ValidateAddresses = (addresses: Record<string, any>): boolean => {
  let deliveryValid = false;
  let invoiceValid = false;

  addresses.forEach((address: Record<string, any>) => {
    if (address.delivery) {
      deliveryValid = true;
    }

    if (address.invoice) {
      invoiceValid = true;
    }
  });

  return deliveryValid && invoiceValid;
};

export default ValidateAddresses;
