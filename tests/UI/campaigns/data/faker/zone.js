const faker = require('faker');

module.exports = class Zone {
  constructor(zoneToCreate = {}) {
    this.name = zoneToCreate.name || `test ${faker.lorem.word()}`;
    this.status = zoneToCreate.status === undefined ? true : zoneToCreate.status;
  }
};
