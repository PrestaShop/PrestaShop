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
