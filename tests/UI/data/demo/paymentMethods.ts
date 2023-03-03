import PaymentMethodData from '@data/faker/paymentMethod';

export default {
  wirePayment: new PaymentMethodData({
    moduleName: 'ps_wirepayment',
    name: 'Bank wire',
    displayName: 'Bank transfer',
  }),
  checkPayment: new PaymentMethodData({
    moduleName: 'ps_checkpayment',
    name: 'Payment by check',
    displayName: 'Payments by check',
  }),
};
