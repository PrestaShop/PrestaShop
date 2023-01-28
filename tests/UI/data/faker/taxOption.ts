// Import data
import TaxOptionCreator from '@data/types/taxOption';

/**
 * Create new tax to use on tax form on BO
 * @class
 */
export default class TaxOptionData {
  public readonly enabled: boolean;

  public readonly displayInShoppingCart: boolean;

  public readonly basedOn: string;

  public readonly useEcoTax: boolean;

  public readonly ecoTax: string|null;

  /**
   * Constructor for class TaxData
   * @param valueToCreate {TaxOptionCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: TaxOptionCreator = {}) {
    /** @type {boolean} Status */
    this.enabled = valueToCreate.enabled === undefined ? true : valueToCreate.enabled;

    /** @type {boolean} Status */
    this.displayInShoppingCart = valueToCreate.displayInShoppingCart === undefined ? false : valueToCreate.displayInShoppingCart;

    /** @type {string} Name of the tax */
    this.basedOn = valueToCreate.basedOn || '';

    /** @type {boolean} */
    this.useEcoTax = valueToCreate.useEcoTax || false;

    /** @type {string|null} Eco Tax */
    this.ecoTax = valueToCreate.ecoTax || null;
  }
}
