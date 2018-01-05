/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
module.exports = {
    aProductWithVariants: {
        id: 5,
        // Format is: key = id of attribute group, value = id of attribute value
        defaultVariant: {
            '1': 1,
            '3': 16
        },
        anotherVariant: {
            '1': 3,
            '3': 14
        }
    },
    aCustomizableProduct: {
      id: 1
    },
    order: {
      id: 5,
      reference: 'KHWLILZLL'
    },
    urls: {
      login: '/en/login',
      myAccount: '/en/my-account',
      myAddresses: '/en/addresses',
      address: '/en/address',
      checkout: '/en/order',
      orderhistory: '/en/order-history',
      orderdetail: '/en/index.php?controller=order-detail&id_order=5',
      aCategoryWithProducts: '/en/3-women',
      identity: '/en/identity',
      adminLogin: '/admin-dev',
      guestTracking: '/en/guest-tracking',
      cart: '/en/cart'
    },
    customer: {
      email: 'pub@prestashop.com',
      password: '123456789'
    }
};
