const faker = require('faker');

/**
 * Create new profile to use on creation form on profile page on BO
 * @class
 */
class ProfileData {
  /**
   * Constructor for class ProfileData
   * @param profileToCreate {Object} Could be used to force the value of some members
   */
  constructor(profileToCreate = {}) {
    /** @type {string} Name of the profile */
    this.name = profileToCreate.name || faker.name.jobType();
  }
}

module.exports = ProfileData;
