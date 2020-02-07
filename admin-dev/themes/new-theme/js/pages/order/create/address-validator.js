/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * Checks if correct addresses are selected.
 * There is a case when options list cannot contain cart addresses 'selected' values
 *  because those are outdated in db (e.g. deleted after cart creation or country is disabled)
 *
 * @param {Array} addresses
 *
 * @returns {boolean}
 */
export const ValidateAddresses = (addresses) => {
  let deliveryValid = false;
  let invoiceValid = false;

  /* eslint-disable-next-line guard-for-in,no-restricted-syntax */
  for (const address in Object.values(addresses)) {
    if (address.delivery) {
      deliveryValid = true;
    }

    if (address.invoice) {
      invoiceValid = true;
    }

    if (deliveryValid && invoiceValid) {
      return true;
    }
  }

  return false;
};

export default ValidateAddresses;
