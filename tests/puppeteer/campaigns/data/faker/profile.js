const faker = require('faker');

module.exports = class Employee {
  constructor(profileToCreate = {}) {
    this.name = profileToCreate.name || faker.name.jobTitle();
  }
};
