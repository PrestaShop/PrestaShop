import StateData from '@data/faker/state';

export default {
  california: new StateData({
    id: 8,
    name: 'California',
    isoCode: 'CA',
    country: 'United States',
    zone: 'North America',
    status: true,
  }),
  bari: new StateData({
    id: 134,
    name: 'Bari',
    isoCode: 'BA',
    country: 'Italy',
    zone: 'Europe',
    status: true,
  }),
  bihar: new StateData({
    id: 8,
    name: 'Bihar',
    isoCode: 'BR',
    country: 'India',
    zone: 'Asia',
    status: true,
  }),
};
