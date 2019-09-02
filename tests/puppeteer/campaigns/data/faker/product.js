let faker = require('faker');

module.exports = class Product {

  constructor(productType = 'Simple product', quantity = false, price = false, productHasCombinations = false, wantedQuantity = '1') {
    this.name = faker.commerce.productName().toUpperCase();
    this.description = faker.lorem.sentence();
    this.reference  = faker.random.alphaNumeric(7);
    this.quantity  = quantity || faker.random.number({'min': 1, 'max': 9}).toString();
    this.quantity_wanted = wantedQuantity;
    this.price  = price || faker.random.number({'min': 10, 'max': 20}).toString();
    this.type = productType;
    this.withCombination  = productHasCombinations;
  }
};
