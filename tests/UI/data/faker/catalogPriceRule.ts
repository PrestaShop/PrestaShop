import Countries from '@data/demo/countries';
import Groups from '@data/demo/groups';
import type CountryData from '@data/faker/country';
import type GroupData from '@data/faker/group';
import type CatalogPriceRuleCreator from '@data/types/catalogPriceRule';

import {faker} from '@faker-js/faker';

const countriesNames: string[] = Object.values(Countries).map((country: CountryData) => country.name);
const groupAccessNames: string[] = Object.values(Groups).map((group: GroupData) => group.name);

const currencies: string[] = ['All currencies', 'Euro'];
const reductionType: string[] = ['Amount', 'Percentage'];
const reductionTax: string[] = ['Tax excluded', 'Tax included'];

/**
 * Create new catalog price rule to use on creation catalog price rule form on BO
 * @class
 */
export default class CatalogPriceRuleData {
  public readonly name: string;

  public readonly currency: string;

  public readonly country: string;

  public readonly group: string;

  public readonly fromQuantity: number;

  public readonly fromDate: string;

  public readonly toDate: string;

  public readonly reductionType: string;

  public readonly reductionTax: string;

  public readonly reduction: number;

  /**
   * Constructor for class CatalogPriceRuleData
   * @param priceRuleToCreate {CatalogPriceRuleCreator} Could be used to force the value of some members
   */
  constructor(priceRuleToCreate: CatalogPriceRuleCreator = {}) {
    /** @type {string} Name of the price rule */
    this.name = priceRuleToCreate.name || faker.commerce.department();

    /** @type {string} Currency of the price rule */
    this.currency = priceRuleToCreate.currency || faker.helpers.arrayElement(currencies);

    /** @type {string} Country that could use the cart rule */
    this.country = priceRuleToCreate.country || faker.helpers.arrayElement(countriesNames);

    /** @type {string} Customer group that could use the price rule */
    this.group = priceRuleToCreate.group || faker.helpers.arrayElement(groupAccessNames);

    /** @type {number} Minimum quantity to apply price rule */
    this.fromQuantity = priceRuleToCreate.fromQuantity === undefined
      ? faker.number.int({min: 1, max: 9})
      : priceRuleToCreate.fromQuantity;

    /** @type {string} Starting date to apply the price rule  */
    this.fromDate = priceRuleToCreate.fromDate || '';

    /** @type {string} Ending date to apply price rule */
    this.toDate = priceRuleToCreate.toDate || '';

    /** @type {string} Reduction type of the price rule */
    this.reductionType = priceRuleToCreate.reductionType || faker.helpers.arrayElement(reductionType);

    /** @type {string} Reduction tax for the price rule */
    this.reductionTax = priceRuleToCreate.reductionTax || faker.helpers.arrayElement(reductionTax);

    /** @type {number} Reduction value of the price rule */
    this.reduction = priceRuleToCreate.reduction || faker.number.int({min: 20, max: 30});
  }
}
