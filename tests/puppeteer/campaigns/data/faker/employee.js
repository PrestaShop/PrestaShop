const faker = require('faker');
const {Profiles} = require('@data/demo/profiles');
const {Languages} = require('@data/demo/languages');
const {Pages} = require('@data/demo/pages');

module.exports = class Employee {
  constructor(employeeToCreate = {}) {
    this.firstName = employeeToCreate.firstName || faker.name.firstName();
    this.lastName = employeeToCreate.lastName || faker.name.lastName();
    this.email = employeeToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = employeeToCreate.password || 'prestashop_demo';
    this.defaultPage = employeeToCreate.defaultPage || faker.random.arrayElement(Pages);
    this.language = employeeToCreate.language || faker.random.arrayElement(Languages);
    this.active = employeeToCreate.active === undefined ? true : employeeToCreate.active;
    this.permissionProfile = employeeToCreate.permissionProfile || faker.random.arrayElement(Profiles);
  }
};
