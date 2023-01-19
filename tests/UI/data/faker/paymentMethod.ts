import type PaymentMethodCreator from '@data/types/paymentMethod';

import {faker} from '@faker-js/faker';

/**
 * @class
 */
export default class PaymentMethodData {
  public readonly name: string;

  public readonly displayName: string;

  public readonly moduleName: string;

  /**
   * Constructor for class PaymentMethodData
   * @param valueToCreate {PaymentMethodCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: PaymentMethodCreator = {}) {
    /** @type {string} */
    this.name = valueToCreate.name || faker.word.noun();

    /** @type {string} */
    this.displayName = valueToCreate.displayName || this.name;

    /** @type {string} */
    this.moduleName = valueToCreate.moduleName || this.name;
  }
}
