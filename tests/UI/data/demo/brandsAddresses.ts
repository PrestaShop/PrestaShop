import BrandAddressData from '@data/faker/brandAddress';

export default {
  first: new BrandAddressData({
    id: 4,
    brandName: 'Studio Design',
    firstName: 'manufacturer',
    lastName: 'manufacturer',
    postalCode: '10154',
    city: 'New York',
    country: 'United States',
  }),
  second: new BrandAddressData({
    id: 3,
    brandName: '',
    firstName: 'supplier',
    lastName: 'supplier',
    postalCode: '10153',
    city: 'New York',
    country: 'United States',
  }),
};
