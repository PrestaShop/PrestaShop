import type RoleCreator from '@data/types/role';

import {faker} from '@faker-js/faker';

/**
 * Create new role to use on creation form on role page on BO
 * @class
 */
export default class RoleData {
  public readonly name: string;

  /**
   * Constructor for class RoleData
   * @param roleToCreate {RoleCreator} Could be used to force the value of some members
   */
  constructor(roleToCreate: RoleCreator = {}) {
    /** @type {string} Name of the profile */
    this.name = roleToCreate.name || faker.person.jobType();
  }
}
