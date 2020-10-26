const faker = require('faker');
const {Products} = require('@data/demo/products');
const {Languages} = require('@data/demo/languages');

const productsNames = Object.values(Products).map(product => product.name);
const LanguagesNames = Object.values(Languages).map(language => language.name);

module.exports = class Supplier {
  constructor(tagsToCreate = {}) {
    this.name = tagsToCreate.name || faker.lorem.word();
    this.language = tagsToCreate.language || faker.random.arrayElement(LanguagesNames);
    this.products = tagsToCreate.products || faker.random.arrayElement(productsNames);
  }
};
