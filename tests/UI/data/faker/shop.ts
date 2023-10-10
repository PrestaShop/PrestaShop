import type ShopCreator from '@data/types/shop';

import {faker} from '@faker-js/faker';

/**
 * Create new shop to use on shop creation form on BO
 * @class
 */
export default class ShopData {
  public readonly name: string;

  public readonly shopGroup: string;

  public readonly color: string;

  public readonly categoryRoot: string;

  /**
   * Constructor for class ShopGroupData
   * @param shopToCreate {ShopCreator} Could be used to force the value of some members
   */
  constructor(shopToCreate: ShopCreator) {
    /** @type {string} Name of the shop */
    this.name = shopToCreate.name || `shop_${faker.lorem.word()}`;

    /** @type {string} Shop group chosen from list */
    this.shopGroup = shopToCreate.shopGroup;

    /** @type {string} Color of the shop */
    this.color = shopToCreate.color || '#00a7ac';

    /** @type {string} Root category of the shop */
    this.categoryRoot = shopToCreate.categoryRoot;
  }
}
