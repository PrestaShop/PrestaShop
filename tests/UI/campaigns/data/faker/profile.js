const faker = require('faker');

module.exports = class Profile {
  constructor(profileToCreate = {}) {
    this.name = profileToCreate.name || faker.name.jobType();
  }
};
