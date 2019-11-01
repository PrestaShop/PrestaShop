const faker = require('faker');

const defaultPage = ['Dashboard', 'Orders', 'Products'];
const language = ['English (English)', 'Français (French)'];
const permissionProfile = ['SuperAdmin', 'Logistician', 'Translator', 'Salesman'];

module.exports = class Employee {
  constructor(employeeToCreate = {}) {
    this.firstName = employeeToCreate.firstName || faker.name.firstName();
    this.lastName = employeeToCreate.lastName || faker.name.lastName();
    this.email = employeeToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = employeeToCreate.password || 'prestashop_demo';
    this.defaultPage = employeeToCreate.defaultPage || faker.random.arrayElement(defaultPage);
    this.language = employeeToCreate.language || faker.random.arrayElement(language);
    this.active = employeeToCreate.active === undefined ? true : employeeToCreate.active;
    this.permissionProfile = employeeToCreate.permissionProfile || faker.random.arrayElement(permissionProfile);
  }
};
