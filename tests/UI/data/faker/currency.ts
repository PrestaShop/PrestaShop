import type {CurrencyCreator} from '@data/types/currency';

import {faker} from '@faker-js/faker';

/**
 * Create new currency to use in currency form on BO
 * @class
 */
export default class CurrencyData {
  public readonly name: string;

  public readonly frName: string;

  public readonly symbol: string;

  public readonly isoCode: string;

  public readonly exchangeRate: number;

  public readonly decimals: number;

  public readonly enabled: boolean;

  /**
   * Constructor for class CurrencyData
   * @param currencyToCreate {CurrencyCreator} Could be used to force the value of some members
   */
  constructor(currencyToCreate: CurrencyCreator = {}) {
    /** @type {string} Name of the currency */
    this.name = currencyToCreate.name || faker.finance.currencyName();

    /** @type {string} */
    this.frName = currencyToCreate.frName || this.name;

    /** @type {string} */
    this.symbol = currencyToCreate.symbol || faker.finance.currencySymbol();

    /** @type {string} */
    this.isoCode = currencyToCreate.isoCode || faker.finance.currencyCode();

    /** @type {number} */
    this.exchangeRate = currencyToCreate.exchangeRate || 1;

    /** @type {number} */
    this.decimals = currencyToCreate.decimals || 2;

    /** @type {boolean} */
    this.enabled = currencyToCreate.enabled === undefined ? true : currencyToCreate.enabled;
  }
}
