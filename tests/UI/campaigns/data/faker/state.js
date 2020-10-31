const faker = require('faker');
const {Zones} = require('@data/demo/zones');

const zones = Object.values(Zones).map(zone => zone.name);
const countriesWithState = [
  'Argentina', 'Australia', 'Canada', 'India', 'Indonesia', 'Italy', 'Japan', 'Mexico', 'United States',
];
const statesIsoCodes = ['IR', 'PK', 'BP', 'BV', 'ZM', 'ZL', 'HM', 'HL', 'BK'];

module.exports = class State {
  constructor(stateToCreate = {}) {
    this.name = stateToCreate.name || `test ${faker.address.state()}`;
    this.isoCode = stateToCreate.isoCode || faker.random.arrayElement(statesIsoCodes);
    this.country = stateToCreate.country || faker.random.arrayElement(countriesWithState);
    this.zone = stateToCreate.zone || faker.random.arrayElement(zones);
    this.active = stateToCreate.active === undefined ? false : stateToCreate.active;
  }
};
