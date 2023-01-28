import type ShopGroupCreator from '@data/types/shopGroup';

import {faker} from '@faker-js/faker';

/**
 * Create new shop group to use on shop group creation form on BO
 * @class
 */
export default class ShopGroupData {
  public readonly name: string;

  public readonly shareCustomer: boolean;

  public readonly shareAvailableQuantities: boolean;

  public readonly status: boolean;

  /**
   * Constructor for class ShopGroupData
   * @param shopGroupToCreate {Object} Could be used to force the value of some members
   */
  constructor(shopGroupToCreate: ShopGroupCreator = {}) {
    /** @type {string} Name of the group */
    this.name = shopGroupToCreate.name || `shop_group_${faker.lorem.word()}`;

    /** @type {boolean} True to share customers between shops of the group */
    this.shareCustomer = shopGroupToCreate.shareCustomer === undefined ? true : shopGroupToCreate.shareCustomer;

    /** @type {boolean} True to share quantities between shops of the group */
    this.shareAvailableQuantities = shopGroupToCreate.shareAvailableQuantities
    === undefined ? true : shopGroupToCreate.shareAvailableQuantities;

    /** @type {boolean} Status of the group */
    this.status = shopGroupToCreate.status === undefined ? true : shopGroupToCreate.status;
  }
}
