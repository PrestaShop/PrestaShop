const faker = require('faker');

const {groupAccess} = require('@data/demo/groupAccess');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);
const groupAccessNames = Object.values(groupAccess).map(group => group.name);

const currencies = ['All currencies', 'Euro'];
const reductionType = ['Amount', 'Percentage'];
const reductionTax = ['Tax excluded', 'Tax included'];

/**
 * Create new catalog price rule to use on creation catalog price rule form on BO
 * @class
 */
class CatalogPriceRuleData {
  /**
   * Constructor for class CatalogPriceRuleData
   * @param priceRuleToCreate {Object} Could be used to force the value of some members
   */
  constructor(priceRuleToCreate = {}) {
    /** @type {string} Name of the price rule */
    this.name = priceRuleToCreate.name || faker.commerce.department();

    /** @type {string} Currency of the price rule */
    this.currency = priceRuleToCreate.currency || faker.random.arrayElement(currencies);

    /** @type {string} Country that could use the cart rule */
    this.country = priceRuleToCreate.country || faker.random.arrayElement(countriesNames);

    /** @type {string} Customer group that could use the price rule */
    this.group = priceRuleToCreate.group || faker.random.arrayElement(groupAccessNames);

    /** @type {number} Minimum quantity to apply price rule */
    this.fromQuantity = priceRuleToCreate.fromQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : priceRuleToCreate.fromQuantity;

    /** @type {string} Starting date to apply the price rule  */
    this.fromDate = priceRuleToCreate.fromDate || '';

    /** @type {string} Ending date to apply price rule */
    this.toDate = priceRuleToCreate.toDate || '';

    /** @type {string} Reduction type of the price rule */
    this.reductionType = priceRuleToCreate.reductionType || faker.random.arrayElement(reductionType);

    /** @type {string} Reduction tax for the price rule */
    this.reductionTax = priceRuleToCreate.reductionTax || faker.random.arrayElement(reductionTax);

    /** @type {number} Reduction value of the price rule */
    this.reduction = priceRuleToCreate.reduction || faker.random.number({min: 20, max: 30});
  }
}

module.exports = CatalogPriceRuleData;
