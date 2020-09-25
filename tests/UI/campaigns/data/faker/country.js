const faker = require('faker');
const {Zones} = require('@data/demo/zones');
const {Currencies} = require('@data/demo/currencies');

const zones = Object.values(Zones).map(zone => zone.name);
const currencies = Object.values(Currencies).map(currency => currency.name);

module.exports = class Country {
  constructor(countryToCreate = {}) {
    this.name = countryToCreate.name || `test${faker.address.country()}`;
    this.isoCode = countryToCreate.isoCode || faker.address.countryCode();
    this.callPrefix = countryToCreate.callPrefix;
    this.currency = countryToCreate.currency || faker.random.arrayElement(currencies);
    this.zone = countryToCreate.zone || faker.random.arrayElement(zones);
    this.needZipCode = countryToCreate.needZipCode === undefined ? false : countryToCreate.needZipCode;
    this.zipCodeFormat = countryToCreate.zipCodeFormat;
    this.active = countryToCreate.active === undefined ? false : countryToCreate.active;
    this.containsStates = countryToCreate.containsStates === undefined ? false : countryToCreate.containsStates;
    this.needIdentificationNumber = countryToCreate.needIdentificationNumber === undefined
      ? false : countryToCreate.needIdentificationNumber;
    this.displayTaxNumber = countryToCreate.displayTaxNumber === undefined ? false : countryToCreate.displayTaxNumber;
  }
};
