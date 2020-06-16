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
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');

module.exports = {
  Orders: {
    firstOrder:
      {
        id: 1,
        ref: 'XKBKNABJK',
        newClient: 'Yes',
        delivery: 'United States',
        customer: 'J. DOE',
        totalPaid: 61.80,
        paymentMethod: PaymentMethods.checkPayment.name,
        status: Statuses.canceled.status,
      },
    secondOrder:
      {
        id: 2,
        ref: 'OHSATSERP',
        newClient: 'No',
        delivery: 'United States',
        customer: 'J. DOE',
        totalPaid: 69.90,
        paymentMethod: PaymentMethods.checkPayment.name,
        status: Statuses.awaitingCheckPayment.status,
      },
    thirdOrder:
      {
        id: 3,
        ref: 'UOYEVOLI',
        newClient: 'No',
        delivery: 'United States',
        customer: 'J. DOE',
        totalPaid: 14.90,
        paymentMethod: PaymentMethods.checkPayment.name,
        status: Statuses.paymentError.status,
      },
    fourthOrder:
      {
        id: 4,
        ref: 'FFATNOMMJ',
        newClient: 'No',
        delivery: 'United States',
        customer: 'J. DOE',
        totalPaid: 14.90,
        paymentMethod: PaymentMethods.checkPayment.name,
        status: Statuses.awaitingCheckPayment.status,
      },
    fifthOrder:
      {
        id: 5,
        ref: 'KHWLILZLL',
        newClient: 'No',
        delivery: 'United States',
        customer: 'J. DOE',
        totalPaid: 20.90,
        paymentMethod: PaymentMethods.wirePayment.name,
        status: Statuses.awaitingCheckPayment.status,
      },
  },
};
