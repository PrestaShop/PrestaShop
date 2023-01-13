import OrderReturnStatusData from '@data/faker/orderReturnStatus';

export default {
  waitingForConfirmation: new OrderReturnStatusData({
    id: 1,
    name: 'Waiting for confirmation',
  }),
  waitingForPackage: new OrderReturnStatusData({
    id: 2,
    name: 'Waiting for package',
  }),
  packageReceived: new OrderReturnStatusData({
    id: 3,
    name: 'Package received',
  }),
  returnDenied: new OrderReturnStatusData({
    id: 4,
    name: 'Return denied',
  }),
  returnCompleted: new OrderReturnStatusData({
    id: 5,
    name: 'Return completed',
  }),
};
