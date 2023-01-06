import type ProfileCreator from '@data/types/profile';

import {faker} from '@faker-js/faker';

/**
 * Create new profile to use on creation form on profile page on BO
 * @class
 */
export default class ProfileData {
  public readonly name: string;

  /**
   * Constructor for class ProfileData
   * @param profileToCreate {Object} Could be used to force the value of some members
   */
  constructor(profileToCreate: ProfileCreator = {}) {
    /** @type {string} Name of the profile */
    this.name = profileToCreate.name || faker.name.jobType();
  }
}
