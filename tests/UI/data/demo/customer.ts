import CustomerData from '@data/faker/customer';

export default {
  johnDoe: new CustomerData({
    id: 2,
    socialTitle: 'Mr.',
    firstName: 'John',
    lastName: 'DOE',
    birthDate: new Date('1970-01-15'),
    email: 'pub@prestashop.com',
    password: '123456789',
    enabled: true,
    newsletter: true,
    partnerOffers: true,
    defaultCustomerGroup: 'Customer',
  }),
};
