import PaymentMethodData from '@data/faker/paymentMethod';

export default {
  cashOnDelivery: new PaymentMethodData({
    moduleName: 'ps_cashondelivery',
    name: 'Cash on delivery (COD)',
    displayName: 'Cash on delivery (COD)',
  }),
  checkPayment: new PaymentMethodData({
    moduleName: 'ps_checkpayment',
    name: 'Payment by check',
    displayName: 'Payments by check',
  }),
  wirePayment: new PaymentMethodData({
    moduleName: 'ps_wirepayment',
    name: 'Bank wire',
    displayName: 'Bank transfer',
  }),
};
