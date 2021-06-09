const faker = require('faker');

/**
 * Create new zone to use on zone form on BO
 * @class
 */
class ZoneData {
  /**
   * Constructor for class ZoneData
   * @param zoneToCreate {Object} Could be used to force the value of some members
   */
  constructor(zoneToCreate = {}) {
    /** @member {string} Name of the zone */
    this.name = zoneToCreate.name || `test ${faker.lorem.word()}`;

    /** @member {boolean} Status of the zone */
    this.status = zoneToCreate.status === undefined ? true : zoneToCreate.status;
  }
}

module.exports = ZoneData;
