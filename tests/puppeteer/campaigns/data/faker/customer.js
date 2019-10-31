const faker = require('faker');

const genders = ['Mr.', 'Mrs.'];
const defaultCustomerGroups = ['Visitor', 'Guest', 'Customer'];

module.exports = class Customer {
  constructor(customerToCreate = {}) {
    this.socialTitle = customerToCreate.socialTitle || faker.random.arrayElement(genders);
    this.firstName = customerToCreate.firstName || faker.name.firstName();
    this.lastName = customerToCreate.lastName || faker.name.lastName();
    this.email = customerToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = customerToCreate.password || '123456789';
    this.birthDate = faker.date.between('1950-01-01', '2000-12-31');
    this.yearOfBirth = customerToCreate.yearOfBirth || this.birthDate.getFullYear().toString();
    this.monthOfBirth = customerToCreate.monthOfBirth || (this.birthDate.getMonth() + 1).toString();
    this.dayOfBirth = customerToCreate.dayOfBirth || this.birthDate.getDate().toString();
    this.enabled = customerToCreate.enabled === undefined ? true : customerToCreate.enabled;
    this.partnerOffers = customerToCreate.partnerOffers === undefined ? true : customerToCreate.partnerOffers;
    this.defaultCustomerGroup = customerToCreate.defaultCustomerGroup
      || faker.random.arrayElement(defaultCustomerGroups);
  }
};
