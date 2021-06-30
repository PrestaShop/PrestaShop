const faker = require('faker');
const {Profiles} = require('@data/demo/profiles');
const {Languages} = require('@data/demo/languages');
const {Pages} = require('@data/demo/pages');

/**
 * Create new employee to use on creation form on employee page on BO
 * @class
 */
class EmployeeData {
  /**
   * Constructor for class EmployeeData
   * @param employeeToCreate {Object} Could be used to force the value of some members
   */
  constructor(employeeToCreate = {}) {
    /** @member {string} Employee fistname */
    this.firstName = employeeToCreate.firstName || faker.name.firstName();

    /** @member {string} Employee lastname */
    this.lastName = employeeToCreate.lastName || faker.name.lastName();

    /** @member {string} Email of the employee */
    this.email = employeeToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @member {string} Password for the employee account */
    this.password = employeeToCreate.password || 'prestashop_demo';

    /** @member {string} Default page where employee should access after login */
    this.defaultPage = employeeToCreate.defaultPage || faker.random.arrayElement(Pages);

    /** @member {string} Default BO language for the employee */
    this.language = employeeToCreate.language
      || faker.random.arrayElement((Object.values(Languages).map(lang => lang.name)).slice(0, 2));

    /** @member {string} Status of the employee */
    this.active = employeeToCreate.active === undefined ? true : employeeToCreate.active;

    /** @member {string} Permission profile to set on the employee */
    this.permissionProfile = employeeToCreate.permissionProfile || faker.random.arrayElement(Profiles);
  }
}

module.exports = EmployeeData;
