const faker = require('faker');

const {countries} = require('@data/demo/countries');

module.exports = class Address {
  constructor(addressToCreate = {}) {
    this.email = addressToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.dni = addressToCreate.dni || '';
    this.alias = addressToCreate.alias || faker.address.streetAddress();
    this.firstName = addressToCreate.firstName || faker.name.firstName();
    this.lastName = addressToCreate.lastName || faker.name.lastName();
    this.company = (addressToCreate.company || faker.company.companyName()).substring(0, 63);
    this.vatNumber = addressToCreate.vatNumber || '';
    this.address = addressToCreate.address || faker.address.streetAddress();
    this.secondAddress = addressToCreate.secondAddress || faker.address.secondaryAddress();
    this.postalCode = addressToCreate.postalCode || faker.address.zipCode('#####');
    this.city = addressToCreate.city || faker.address.city();
    this.country = addressToCreate.country || faker.random.arrayElement(countries);
    this.phone = addressToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.other = addressToCreate.other || '';
  }
};
