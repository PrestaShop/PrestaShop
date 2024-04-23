import {
  // Import data
  FakerAddress,
} from '@prestashop-core/ui-testing';

export default {
  first: new FakerAddress({
    id: 1,
    firstName: 'Anonymous',
    lastName: 'Anonymous',
    address: 'Anonymous',
    postalCode: '00000',
    city: 'Anonymous',
    country: 'France',
  }),
  second: new FakerAddress({
    id: 2,
    name: 'Mon adresse',
    firstName: 'John',
    lastName: 'DOE',
    company: 'My Company',
    address: '16, Main street',
    secondAddress: '2nd floor',
    postalCode: '75002',
    city: 'Paris',
    country: 'France',
    phone: '0102030405',
  }),
  third: new FakerAddress({
    id: 5,
    dni: '',
    alias: 'My address',
    firstName: 'John',
    lastName: 'DOE',
    company: 'My Company',
    vatNumber: '',
    address: '16, Main street',
    secondAddress: '2nd floor',
    postalCode: '33133',
    city: 'Miami',
    state: 'Florida',
    country: 'United States',
    phone: '0102030405',
    other: '',
  }),
};
