import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

export default {
  firstOrder: new OrderData({
    id: 1,
    reference: 'XKBKNABJK',
    newClient: true,
    delivery: 'United States',
    customer: dataCustomers.johnDoe,
    totalPaid: 61.80,
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.canceled,
  }),
  secondOrder: new OrderData({
    id: 2,
    reference: 'OHSATSERP',
    newClient: false,
    delivery: 'United States',
    customer: dataCustomers.johnDoe,
    totalPaid: 69.90,
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.awaitingCheckPayment,
  }),
  thirdOrder: new OrderData({
    id: 3,
    reference: 'UOYEVOLI',
    newClient: false,
    delivery: 'United States',
    customer: dataCustomers.johnDoe,
    totalPaid: 14.90,
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.paymentError,
  }),
  fourthOrder: new OrderData({
    id: 4,
    reference: 'FFATNOMMJ',
    newClient: false,
    delivery: 'United States',
    customer: dataCustomers.johnDoe,
    totalPaid: 14.90,
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.awaitingCheckPayment,
  }),
  fifthOrder: new OrderData({
    id: 5,
    reference: 'KHWLILZLL',
    newClient: false,
    delivery: 'United States',
    customer: dataCustomers.johnDoe,
    totalPaid: 20.90,
    paymentMethod: dataPaymentMethods.wirePayment,
    status: dataOrderStatuses.awaitingCheckPayment,
  }),
};
