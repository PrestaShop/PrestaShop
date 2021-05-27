const faker = require('faker');
const {Zones} = require('@data/demo/zones');

const zones = Object.values(Zones).map(zone => zone.name);
const countriesWithState = [
  'Argentina', 'Australia', 'Canada', 'India', 'Indonesia', 'Italy', 'Japan', 'Mexico', 'United States',
];
const statesIsoCodes = ['IR', 'PK', 'BP', 'BV', 'ZM', 'ZL', 'HM', 'HL', 'BK'];

/**
 * Create new state to use on state creation form on BO
 * @class
 */
class StateData {
  /**
   * Constructor for class StateData
   * @param stateToCreate {Object} Could be used to force the value of some members
   */
  constructor(stateToCreate = {}) {
    /** @member {string} Name of the state */
    this.name = stateToCreate.name || `test ${faker.address.state()}`;

    /** @member {string} Iso code of the state */
    this.isoCode = stateToCreate.isoCode || faker.random.arrayElement(statesIsoCodes);

    /** @member {string} Country of the state */
    this.country = stateToCreate.country || faker.random.arrayElement(countriesWithState);

    /** @member {string} Zone of the state */
    this.zone = stateToCreate.zone || faker.random.arrayElement(zones);

    /** @member {boolean} Status of the state */
    this.active = stateToCreate.active === undefined ? false : stateToCreate.active;
  }
}

module.exports = StateData;
