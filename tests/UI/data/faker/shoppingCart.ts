// Import data
import Carriers from '@data/demo/carriers';
import type CarrierData from '@data/faker/carrier';
import type CustomerData from '@data/faker/customer';
import type {ShoppingCartCreator} from '@data/types/shoppingCart';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

/**
 * Create new tax rule to use on tax rule form on BO
 * @class
 */
export default class ShoppingCartData {
  public readonly id: number;

  public readonly orderID: number;

  public readonly customer: CustomerData;

  public readonly carrier: CarrierData;

  public readonly online: boolean;

  /**
   * Constructor for class ShoppingCartData
   * @param valueToCreate {ShoppingCartCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: ShoppingCartCreator = {}) {
    /** @type {number} ID */
    this.id = valueToCreate.id || 0;

    /** @type {number} Order ID */
    this.orderID = valueToCreate.orderID || 0;

    /** @type {CustomerData} Customer */
    this.customer = valueToCreate.customer || dataCustomers.johnDoe;

    /** @type {CarrierData} Carrier */
    this.carrier = valueToCreate.carrier || Carriers.myCarrier;

    /** @type {boolean} */
    this.online = valueToCreate.online || true;
  }
}
