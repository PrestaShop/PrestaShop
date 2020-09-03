const faker = require('faker');

const {countries} = require('@data/demo/countries');

module.exports = class Store {
  constructor(storeContactToCreate = {}) {
    this.name = storeContactToCreate.name || faker.company.companyName();
    this.email = storeContactToCreate.email || faker.internet.email();
    this.registrationNumber = storeContactToCreate.registrationNumber;
    this.address1 = storeContactToCreate.address || faker.address.streetAddress();
    this.address2 = storeContactToCreate.secondAddress || faker.address.secondaryAddress();
    this.postcode = storeContactToCreate.postalCode || faker.address.zipCode('#####');
    this.city = storeContactToCreate.city || faker.address.city();
    this.country = storeContactToCreate.country || faker.random.arrayElement(countries);
    this.phone = storeContactToCreate.phone || faker.phone.phoneNumber('01########');
    this.fax = storeContactToCreate.fax || faker.phone.phoneNumber('01########');
  }
};
