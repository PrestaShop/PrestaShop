const faker = require('faker');

module.exports = class Store {
  constructor(storeToCreate = {}) {
    this.name = storeToCreate.name || faker.company.companyName();

    this.address1 = storeToCreate.address || faker.address.streetAddress();
    this.address2 = storeToCreate.secondAddress || faker.address.secondaryAddress();
    this.postcode = storeToCreate.postalCode || faker.address.zipCode('#####');
    this.city = storeToCreate.city || faker.address.city();
    this.country = storeToCreate.country || 'France';
    this.latitude = storeToCreate.latitude || faker.address.latitude();
    this.longitude = storeToCreate.longitude || faker.address.longitude();

    this.phone = storeToCreate.phone || faker.phone.phoneNumber('01########');
    this.fax = storeToCreate.fax || faker.phone.phoneNumber('01########');

    this.email = storeToCreate.email || faker.internet.email();
    this.note = storeToCreate.note || faker.lorem.sentence();

    this.status = storeToCreate.status === undefined ? true : storeToCreate.status;

    this.hours = storeToCreate.hours || new Array(7).fill('10:00 - 18:00', 0, 7);
  }
};
