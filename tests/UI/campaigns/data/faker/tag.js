const faker = require('faker');
const {Products} = require('@data/demo/products');
const {Languages} = require('@data/demo/languages');

const productsNames = Object.values(Products).map(product => product.name);
const languagesNames = Object.values(Languages).map(language => language.name);

module.exports = class Supplier {
  constructor(tagsToCreate = {}) {
    this.name = tagsToCreate.name || `new_tag_${faker.lorem.word()}`;
    this.language = tagsToCreate.language || faker.random.arrayElement(languagesNames);
    this.products = tagsToCreate.products || faker.random.arrayElement(productsNames);
  }
};
