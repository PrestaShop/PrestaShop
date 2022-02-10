const faker = require('faker');

const {Titles} = require('@data/demo/titles');
const {groupAccess} = require('@data/demo/groupAccess');

const genders = Object.values(Titles).map(title => title.name);
const groups = Object.values(groupAccess).map(group => group.name);

/**
 * Create new customer to use on creation form on customer page on BO and FO
 * @class
 */
class CustomerData {
  /**
   * Constructor for class CustomerData
   * @param customerToCreate {Object} Could be used to force the value of some members
   */
  constructor(customerToCreate = {}) {
    /** @type {string} Social title of the customer (Mr, Mrs) */
    this.socialTitle = customerToCreate.socialTitle || faker.random.arrayElement(genders);

    /** @type {string} Firstname of the customer */
    this.firstName = customerToCreate.firstName || faker.name.firstName();

    /** @type {string} Lastname of the customer */
    this.lastName = customerToCreate.lastName || faker.name.lastName();

    /** @type {string} Email for the customer account */
    this.email = customerToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @type {string} Password for the customer account */
    this.password = customerToCreate.password === undefined ? '123456789' : customerToCreate.password;

    /** @type {Date} Birthdate of the customer */
    this.birthDate = faker.date.between('1950-01-01', '2000-12-31');

    /** @type {string} Year of the birth 'yyyy' */
    this.yearOfBirth = customerToCreate.yearOfBirth || this.birthDate.getFullYear().toString();

    /** @type {string} Month of the birth 'mm' */
    this.monthOfBirth = customerToCreate.monthOfBirth || (this.birthDate.getMonth() + 1).toString();

    /** @type {string} Day of the birth 'dd'  */
    this.dayOfBirth = customerToCreate.dayOfBirth || this.birthDate.getDate().toString();

    /** @type {boolean} Status of the customer */
    this.enabled = customerToCreate.enabled === undefined ? true : customerToCreate.enabled;

    /** @type {boolean} True to enable partner offers */
    this.partnerOffers = customerToCreate.partnerOffers === undefined ? true : customerToCreate.partnerOffers;

    /** @type {string} Default group for the customer */
    this.defaultCustomerGroup = customerToCreate.defaultCustomerGroup || faker.random.arrayElement(groups);

    /** @type {boolean} True to enable sending newsletter to the customer */
    this.newsletter = customerToCreate.newsletter === undefined ? false : customerToCreate.newsletter;
  }
}

module.exports = CustomerData;
