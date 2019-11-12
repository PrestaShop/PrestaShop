const faker = require('faker');

const {countries} = require('@data/demo/countries');

module.exports = class brandAddress {
  constructor(brandAddressToCreate = {}) {
    this.brandName = brandAddressToCreate.brandName || '--';
    this.firstName = brandAddressToCreate.firstName || faker.name.firstName();
    this.lastName = brandAddressToCreate.lastName || faker.name.lastName();
    this.address = brandAddressToCreate.address || faker.address.streetAddress();
    this.secondaryAddress = brandAddressToCreate.secondaryAddress || faker.address.secondaryAddress();
    this.postalCode = brandAddressToCreate.postalCode || faker.address.zipCode();
    this.city = brandAddressToCreate.city || faker.address.city();
    this.country = brandAddressToCreate.country || faker.random.arrayElement(countries);
    this.homePhone = brandAddressToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.mobilePhone = brandAddressToCreate.mobilePhone || faker.phone.phoneNumber('06########');
    this.other = brandAddressToCreate.other || '';
  }
};
