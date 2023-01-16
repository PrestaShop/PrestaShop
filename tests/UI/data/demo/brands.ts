import BrandData from '@data/faker/brand';

export default {
  first: new BrandData({
    id: 1,
    name: 'Studio Design',
    addresses: 1,
    products: 9,
    enabled: true,
  }),
  second: new BrandData({
    id: 2,
    name: 'Graphic Corner',
    addresses: 0,
    products: 9,
    enabled: true,
  }),
};
