import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';

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
        status: OrderStatuses.canceled.name,
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
        status: OrderStatuses.awaitingCheckPayment.name,
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
        status: OrderStatuses.paymentError.name,
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
        status: OrderStatuses.awaitingCheckPayment.name,
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
        status: OrderStatuses.awaitingCheckPayment.name,
      },
  },
};
