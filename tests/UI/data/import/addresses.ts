import ImportData from '@data/faker/import';
import type {ImportAddress} from '@data/types/import';

import {
  // Import data
  dataCountries,
  dataCustomers,
  type FakerCountry,
} from '@prestashop-core/ui-testing';

import {fakerFR as faker} from '@faker-js/faker';

const countriesNames = Object.values(dataCountries).map((country: FakerCountry) => country.name);

const records: ImportAddress[] = [];

function createRecord(): ImportAddress[] {
  for (let i: number = 0; i < 10; i++) {
    records.push({
      id: i + 3,
      alias: faker.location.streetAddress().substring(0, 30),
      active: faker.number.int({min: 0, max: 1}),
      email: dataCustomers.johnDoe.email,
      customerID: dataCustomers.johnDoe.id,
      manufacturer: '',
      supplier: '',
      company: faker.company.name(),
      lastname: 'test',
      firstname: faker.person.firstName(),
      address1: faker.location.streetAddress(),
      address2: faker.location.secondaryAddress(),
      zipCode: faker.location.zipCode('#####'),
      city: faker.location.city(),
      country: faker.helpers.arrayElement(countriesNames),
      state: '',
      other: '',
      phone: faker.phone.number(),
      mobilePhone: faker.phone.number(),
      vatNumber: '',
      dni: '',
    });
  }

  return records;
}

export default new ImportData({
  entity: 'Addresses',
  header: [
    {id: 'id', title: 'Address ID'},
    {id: 'alias', title: 'Alias*'},
    {id: 'active', title: 'Active (0/1)'},
    {id: 'email', title: 'Customer e-mail*'},
    {id: 'customerID', title: 'Customer ID*'},
    {id: 'manufacturer', title: 'Manufacturer'},
    {id: 'supplier', title: 'Supplier'},
    {id: 'company', title: 'Company'},
    {id: 'lastname', title: 'Lastname*'},
    {id: 'firstname', title: 'Firstname*'},
    {id: 'address1', title: 'Address 1*'},
    {id: 'address2', title: 'Address 2'},
    {id: 'zipCode', title: 'Zipcode*'},
    {id: 'city', title: 'City*'},
    {id: 'country', title: 'Country*'},
    {id: 'state', title: 'State'},
    {id: 'other', title: 'Other'},
    {id: 'phone', title: 'Phone'},
    {id: 'mobilePhone', title: 'Mobile Phone'},
    {id: 'vatNumber', title: 'VAT number'},
    {id: 'dni', title: 'DNI'},
  ],
  records: createRecord(),
});
