const faker = require('faker');
const {Zones} = require('@data/demo/zones');

const zones = Object.values(Zones).map(zone => zone.name);
const countriesWithState = [
  'Argentina', 'Australia', 'Canada', 'India', 'Indonesia', 'Italy', 'Japan', 'Mexico', 'United States',
];

module.exports = class Country {
  constructor(stateToCreate = {}) {
    this.name = stateToCreate.name || faker.address.state();
    this.isoCode = stateToCreate.isoCode || faker.address.countryCode();
    this.country = stateToCreate.country || faker.random.arrayElement(countriesWithState);
    this.zone = stateToCreate.zone || faker.random.arrayElement(zones);
    this.active = stateToCreate.active === undefined ? false : stateToCreate.active;
  }
};
