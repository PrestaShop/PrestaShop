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
    /** @member {string} Related customer email */
    this.email = addressToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @member {string} Tax identification number of the customer */
    this.dni = addressToCreate.dni || '';

    /** @member {string} Address alias or name */
    this.alias = addressToCreate.alias || faker.address.streetAddress();

    /** @member {string} Customer firstname */
    this.firstName = addressToCreate.firstName || faker.name.firstName();

    /** @member {string} Customer lastname */
    this.lastName = addressToCreate.lastName || faker.name.lastName();

    /** @member {string} Company name if it's a company address */
    this.company = (addressToCreate.company || faker.company.companyName()).substring(0, 63);

    /** @member {string} Tax identification number if it's a company */
    this.vatNumber = addressToCreate.vatNumber || '';

    /** @member {string} Address first line */
    this.address = addressToCreate.address || faker.address.streetAddress();

    /** @member {string} Address second line */
    this.secondAddress = addressToCreate.secondAddress || faker.address.secondaryAddress();

    /** @member {string} Address postal code (default to this format #####) */
    this.postalCode = addressToCreate.postalCode || faker.address.zipCode('#####');

    /** @member {string} Address city name */
    this.city = addressToCreate.city || faker.address.city();

    /** @member {string} Address country name */
    this.country = addressToCreate.country || faker.random.arrayElement(countriesNames);

    /** @member {string} Phone number */
    this.phone = addressToCreate.homePhone || faker.phone.phoneNumber('01########');

    /** @member {string} Other information to add on address */
    this.other = addressToCreate.other || '';
  }
}

module.exports = AddressData;
