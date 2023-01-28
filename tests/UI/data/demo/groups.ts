import GroupData from '@data/faker/group';

export default {
  visitor: new GroupData({
    id: 1,
    name: 'Visitor',
    discount: 0,
    shownPrices: true,
  }),
  guest: new GroupData({
    id: 2,
    name: 'Guest',
    discount: 0,
    shownPrices: true,
  }),
  customer: new GroupData({
    id: 3,
    name: 'Customer',
    discount: 0,
    shownPrices: true,
  }),
};
