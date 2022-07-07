const {faker} = require('@faker-js/faker');
const {DefaultCustomer} = require('@data/demo/customer');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);

const records = [];

function createRecord() {
  for (let i = 0; i < 10; i++) {
    records.push({
      id: i + 3,
      alias: faker.address.streetAddress(),
      active: faker.datatype.number({min: 0, max: 1}),
      email: DefaultCustomer.email,
      customerID: DefaultCustomer.id,
      manufacturer: '',
      supplier: '',
      company: faker.company.companyName(),
      lastname: 'test',
      firstname: faker.name.firstName(),
      address1: faker.address.streetAddress(),
      address2: faker.address.secondaryAddress(),
      zipCode: faker.address.zipCode('#####'),
      city: faker.address.city(),
      country: faker.helpers.arrayElement(countriesNames),
      state: '',
      other: '',
      phone: faker.phone.number('01########'),
      mobilePhone: faker.phone.number('01########'),
      vatNumber: '',
      dni: '',
    });
  }
  return records;
}

module.exports = {
  Data: {
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
  },
};
