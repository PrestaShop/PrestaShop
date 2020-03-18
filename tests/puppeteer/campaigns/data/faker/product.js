const faker = require('faker');

module.exports = class Product {
  constructor(productToCreate) {
    this.name = (productToCreate.name || faker.commerce.productName()).toUpperCase();
    this.type = productToCreate.type;
    this.status = productToCreate.status === undefined ? true : productToCreate.status;
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;
    this.reference = faker.random.alphaNumeric(7);
    this.quantity = productToCreate.quantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.quantity;
    this.price = productToCreate.price === undefined ? faker.random.number({min: 10, max: 20}) : productToCreate.price;
    this.combinations = productToCreate.combinations || {
      Color: ['White', 'Black'],
      Size: ['S', 'M'],
    };
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';
    this.specificPrice = productToCreate.specificPrice || {
      combinations: 'Size - S, Color - White',
      discount: faker.random.number({min: 10, max: 100}),
      startingAt: faker.random.number({min: 2, max: 5}),
    };
  }
};
