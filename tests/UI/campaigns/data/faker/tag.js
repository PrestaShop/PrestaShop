const faker = require('faker');
const {products} = require('@data/demo/products');
const {languages} = require('@data/demo/languages');

const productsNames = Object.values(products).map(product => product.name);
const LanguagesNames = Object.values(languages).map(language => language.name);

module.exports = class Supplier {
  constructor(tagsToCreate = {}) {
    this.name = tagsToCreate.name || faker.lorem.word();
    this.language = tagsToCreate.language || faker.random.arrayElement(LanguagesNames);
    this.products = tagsToCreate.products || faker.random.arrayElement(productsNames);
  }
};
