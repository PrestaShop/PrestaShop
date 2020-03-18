const faker = require('faker');

const {groupAccess} = require('@data/demo/groupAccess');
const {countries} = require('@data/demo/countries');
const currencies = ['All currencies', 'Euro'];
const reductionType = ['Amount', 'Percentage'];

module.exports = class Category {
  constructor(priceRuleToCreate = {}) {
    this.name = priceRuleToCreate.name || faker.commerce.department();
    this.currency = priceRuleToCreate.currency || faker.random.arrayElement(currencies);
    this.country = priceRuleToCreate.country || faker.random.arrayElement(countries);
    this.group = priceRuleToCreate.group || faker.random.arrayElement(groupAccess);
    this.fromQuantity = priceRuleToCreate.fromQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : priceRuleToCreate.fromQuantity;
    this.reductionType = priceRuleToCreate.reductionType || faker.random.arrayElement(reductionType);
  }
};
