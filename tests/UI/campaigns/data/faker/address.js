const faker = require('faker');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);

/**
 * Create new address to use in customer address form on BO and FO
 * @class
 */
class AddressData {
  /**
   * Constructor for class AddressData
   * @param addressToCreate {Object} Could be used to force the value of some members
   */
  constructor(addressToCreate = {}) {
    /** @type {string} Related customer email */
    this.email = addressToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @type {string} Tax identification number of the customer */
    this.dni = addressToCreate.dni || '';

    /** @type {string} Address alias or name */
    this.alias = addressToCreate.alias || faker.address.streetAddress();

    /** @type {string} Customer firstname */
    this.firstName = addressToCreate.firstName || faker.name.firstName();

    /** @type {string} Customer lastname */
    this.lastName = addressToCreate.lastName || faker.name.lastName();

    /** @type {string} Company name if it's a company address */
    this.company = (addressToCreate.company || faker.company.companyName()).substring(0, 63);

    /** @type {string} Tax identification number if it's a company */
    this.vatNumber = addressToCreate.vatNumber || '';

    /** @type {string} Address first line */
    this.address = addressToCreate.address || faker.address.streetAddress();

    /** @type {string} Address second line */
    this.secondAddress = addressToCreate.secondAddress || faker.address.secondaryAddress();

    /** @type {string} Address postal code (default to this format #####) */
    this.postalCode = addressToCreate.postalCode || faker.address.zipCode('#####');

    /** @type {string} Address city name */
    this.city = addressToCreate.city || faker.address.city();

    /** @type {string} Address country name */
    this.country = addressToCreate.country || faker.random.arrayElement(countriesNames);

    /** @type {string} Phone number */
    this.phone = addressToCreate.homePhone || faker.phone.phoneNumber('01########');

    /** @type {string} Other information to add on address */
    this.other = addressToCreate.other || '';
  }
}

module.exports = AddressData;
