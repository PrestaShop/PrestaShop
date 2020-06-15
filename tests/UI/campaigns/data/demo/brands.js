/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
module.exports = {
  demoBrands: {
    first: {
      id: '1',
      name: 'Studio Design',
      addresses: 1,
      products: 9,
      enabled: true,
    },
    second: {
      id: '2',
      name: 'Graphic Corner',
      addresses: 0,
      products: 9,
      enabled: true,
    },
  },
  demoAddresses: {
    first: {
      id: '4',
      brand: 'Studio Design',
      firstName: 'manufacturer',
      lastName: 'manufacturer',
      postalCode: '10154',
      city: 'New York',
      country: 'United States',
    },
    second: {
      id: '3',
      brand: '',
      firstName: 'supplier',
      lastName: 'supplier',
      postalCode: '10153',
      city: 'New York',
      country: 'United States',
    },
  },
};
