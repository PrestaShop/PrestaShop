import GroupCreator from '@data/types/group';

import {faker} from '@faker-js/faker';

const priceDisplayMethod: string[] = ['Tax included', 'Tax excluded'];

/**
 * Create new group to use on creation form on group page on BO
 * @class
 */
export default class GroupData {
  public readonly id: number;

  public readonly name: string;

  public readonly frName: string;

  public readonly discount: number;

  public readonly priceDisplayMethod: string;

  public readonly shownPrices: boolean;

  /**
   * Constructor for class GroupData
   * @param groupToCreate {GroupCreator} Could be used to force the value of some members
   */
  constructor(groupToCreate: GroupCreator = {}) {
    /** @type {number} ID of the group */
    this.id = groupToCreate.id || 0;

    /** @type {string} Name of the group */
    this.name = groupToCreate.name || faker.person.jobType();

    /** @type {string} French name of the group */
    this.frName = groupToCreate.frName || this.name;

    /** @type {number} Basic discount for the group */
    this.discount = groupToCreate.discount || 0;

    /** @type {string} Price display method of the group */
    this.priceDisplayMethod = groupToCreate.priceDisplayMethod || faker.helpers.arrayElement(priceDisplayMethod);

    /** @type {boolean} True to show prices for the group */
    this.shownPrices = groupToCreate.shownPrices === undefined ? true : groupToCreate.shownPrices;
  }
}
