const faker = require('faker');
const {Zones} = require('@data/demo/zones');
const {Currencies} = require('@data/demo/currencies');

const zones = Object.values(Zones).map(zone => zone.name);
const currencies = Object.values(Currencies).map(currency => currency.name);

/**
 * Create new country to use on creation form on country page on BO
 * @class
 */
class CountryData {
  /**
   * Constructor for class CountryData
   * @param countryToCreate {Object} Could be used to force the value of some members
   */
  constructor(countryToCreate = {}) {
    /** @member {string} Name of the country */
    this.name = countryToCreate.name || `test${faker.address.country()}`;

    /** @member {string} Country iso code */
    this.isoCode = countryToCreate.isoCode || faker.address.countryCode();

    /** @member {string} Country call Prefix */
    this.callPrefix = countryToCreate.callPrefix;

    /** @member {string} Currency used in the country */
    this.currency = countryToCreate.currency || faker.random.arrayElement(currencies);

    /** @member {string} In which zone the country belongs */
    this.zone = countryToCreate.zone || faker.random.arrayElement(zones);

    /** @member {string} True if the country used zip codes */
    this.needZipCode = countryToCreate.needZipCode === undefined ? false : countryToCreate.needZipCode;

    /** @member {string} Format of the zip code if used */
    this.zipCodeFormat = countryToCreate.zipCodeFormat;

    /** @member {string} Status of the country */
    this.active = countryToCreate.active === undefined ? false : countryToCreate.active;

    /** @member {string} True if the country have states */
    this.containsStates = countryToCreate.containsStates === undefined ? false : countryToCreate.containsStates;

    /** @member {string} True if need identification number for the country */
    this.needIdentificationNumber = countryToCreate.needIdentificationNumber === undefined
      ? false : countryToCreate.needIdentificationNumber;

    /** @member {string} True to display tax number when located on the country */
    this.displayTaxNumber = countryToCreate.displayTaxNumber === undefined ? false : countryToCreate.displayTaxNumber;
  }
}

module.exports = CountryData;
