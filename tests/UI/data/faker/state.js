const {faker} = require('@faker-js/faker');
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
    /** @type {string} Name of the state */
    this.name = stateToCreate.name || `test ${faker.address.state()}`;

    /** @type {string} Iso code of the state */
    this.isoCode = stateToCreate.isoCode || faker.helpers.arrayElement(statesIsoCodes);

    /** @type {string} Country of the state */
    this.country = stateToCreate.country || faker.helpers.arrayElement(countriesWithState);

    /** @type {string} Zone of the state */
    this.zone = stateToCreate.zone || faker.helpers.arrayElement(zones);

    /** @type {boolean} Status of the state */
    this.status = stateToCreate.active === undefined ? false : stateToCreate.status;
  }
}

module.exports = StateData;
