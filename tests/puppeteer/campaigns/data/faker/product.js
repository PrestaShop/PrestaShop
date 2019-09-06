const faker = require('faker');

module.exports = class Product {
  constructor(productToCreate) {
    this.name = productToCreate.name || faker.commerce.productName().toUpperCase();
    this.description = faker.lorem.sentence();
    this.reference = faker.random.alphaNumeric(7);
    this.quantity = productToCreate.quantity || faker.random.number({min: 1, max: 9}).toString();
    this.quantity_wanted = productToCreate.wantedQuantity || '1';
    this.price = productToCreate.price || faker.random.number({min: 10, max: 20}).toString();
    this.type = productToCreate.type;
    this.withCombination = productToCreate.productHasCombinations;
    this.combinations = productToCreate.combinations || {
      Color: ['White', 'Black'],
      Size: ['S', 'M'],
    };
  }
};
