const faker = require('faker');

const {Titles} = require('@data/demo/titles');
const {groupAccess} = require('@data/demo/groupAccess');


const genders = Object.values(Titles).map(title => title.name);
const groups = Object.values(groupAccess).map(group => group.name);

module.exports = class Customer {
  constructor(customerToCreate = {}) {
    this.socialTitle = customerToCreate.socialTitle || faker.random.arrayElement(genders);
    this.firstName = customerToCreate.firstName || faker.name.firstName();
    this.lastName = customerToCreate.lastName || faker.name.lastName();
    this.email = customerToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = customerToCreate.password === undefined ? '123456789' : customerToCreate.password;
    this.birthDate = faker.date.between('1950-01-01', '2000-12-31');
    this.yearOfBirth = customerToCreate.yearOfBirth || this.birthDate.getFullYear().toString();
    this.monthOfBirth = customerToCreate.monthOfBirth || (this.birthDate.getMonth() + 1).toString();
    this.dayOfBirth = customerToCreate.dayOfBirth || this.birthDate.getDate().toString();
    this.enabled = customerToCreate.enabled === undefined ? true : customerToCreate.enabled;
    this.partnerOffers = customerToCreate.partnerOffers === undefined ? true : customerToCreate.partnerOffers;
    this.defaultCustomerGroup = customerToCreate.defaultCustomerGroup || faker.random.arrayElement(groups);
    this.newsletter = customerToCreate.newsletter === undefined ? false : customerToCreate.newsletter;
  }
};
