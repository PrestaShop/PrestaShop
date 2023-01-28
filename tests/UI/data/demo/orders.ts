import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import OrderData from '@data/faker/order';
import Customers from '@data/demo/customers';

export default {
  firstOrder: new OrderData({
    id: 1,
    reference: 'XKBKNABJK',
    newClient: true,
    delivery: 'United States',
    customer: Customers.johnDoe,
    totalPaid: 61.80,
    paymentMethod: PaymentMethods.checkPayment,
    status: OrderStatuses.canceled,
  }),
  secondOrder: new OrderData({
    id: 2,
    reference: 'OHSATSERP',
    newClient: false,
    delivery: 'United States',
    customer: Customers.johnDoe,
    totalPaid: 69.90,
    paymentMethod: PaymentMethods.checkPayment,
    status: OrderStatuses.awaitingCheckPayment,
  }),
  thirdOrder: new OrderData({
    id: 3,
    reference: 'UOYEVOLI',
    newClient: false,
    delivery: 'United States',
    customer: Customers.johnDoe,
    totalPaid: 14.90,
    paymentMethod: PaymentMethods.checkPayment,
    status: OrderStatuses.paymentError,
  }),
  fourthOrder: new OrderData({
    id: 4,
    reference: 'FFATNOMMJ',
    newClient: false,
    delivery: 'United States',
    customer: Customers.johnDoe,
    totalPaid: 14.90,
    paymentMethod: PaymentMethods.checkPayment,
    status: OrderStatuses.awaitingCheckPayment,
  }),
  fifthOrder: new OrderData({
    id: 5,
    reference: 'KHWLILZLL',
    newClient: false,
    delivery: 'United States',
    customer: Customers.johnDoe,
    totalPaid: 20.90,
    paymentMethod: PaymentMethods.wirePayment,
    status: OrderStatuses.awaitingCheckPayment,
  }),
};
