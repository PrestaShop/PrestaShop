const faker = require('faker');

const {groupAccess} = require('@data/demo/groupAccess');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);
const groupAccessNames = Object.values(groupAccess).map(group => group.name);

const currencies = ['All currencies', 'Euro'];
const reductionType = ['Amount', 'Percentage'];
const reductionTax = ['Tax excluded', 'Tax included'];

module.exports = class Category {
  constructor(priceRuleToCreate = {}) {
    this.name = priceRuleToCreate.name || faker.commerce.department();
    this.currency = priceRuleToCreate.currency || faker.random.arrayElement(currencies);
    this.country = priceRuleToCreate.country || faker.random.arrayElement(countriesNames);
    this.group = priceRuleToCreate.group || faker.random.arrayElement(groupAccessNames);
    this.fromQuantity = priceRuleToCreate.fromQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : priceRuleToCreate.fromQuantity;
    this.fromDate = priceRuleToCreate.fromDate || '';
    this.toDate = priceRuleToCreate.toDate || '';
    this.reductionType = priceRuleToCreate.reductionType || faker.random.arrayElement(reductionType);
    this.reductionTax = priceRuleToCreate.reductionTax || faker.random.arrayElement(reductionTax);
    this.reduction = priceRuleToCreate.reduction || faker.random.number({min: 20, max: 30});
  }
};
