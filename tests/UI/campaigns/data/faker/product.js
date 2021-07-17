const faker = require('faker');

const behavior = ['Deny orders', 'Allow orders', 'Default behavior'];

module.exports = class Product {
  constructor(productToCreate) {
    this.name = (productToCreate.name || faker.commerce.productName()).toUpperCase();
    this.coverImage = productToCreate.coverImage || null;
    this.thumbImage = productToCreate.thumbImage || null;
    this.type = productToCreate.type;
    this.status = productToCreate.status === undefined ? true : productToCreate.status;
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;
    this.reference = productToCreate.reference || faker.random.alphaNumeric(7);
    this.quantity = productToCreate.quantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.quantity;
    this.price = productToCreate.price === undefined ? faker.random.number({min: 10, max: 20}) : productToCreate.price;
    this.combinations = productToCreate.combinations || {
      Color: ['White', 'Black'],
      Size: ['S', 'M'],
    };
    this.pack = productToCreate.pack || {
      demo_1: faker.random.number({min: 10, max: 100}),
      demo_2: faker.random.number({min: 10, max: 100}),
    };
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';
    this.specificPrice = productToCreate.specificPrice || {
      combinations: 'Size - S, Color - White',
      discount: faker.random.number({min: 10, max: 100}),
      startingAt: faker.random.number({min: 2, max: 5}),
    };
    this.minimumQuantity = productToCreate.minimumQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.minimumQuantity;
    this.stockLocation = productToCreate.stockLocation || 'Stock location';
    this.lowStockLevel = productToCreate.lowStockLevel;
    this.labelWhenInStock = productToCreate.labelWhenInStock || 'Label when in stock';
    this.LabelWhenOutOfStock = productToCreate.LabelWhenOutOfStock || 'Label when out of stock';
    this.behaviourOutOfStock = productToCreate.behaviourOutOfStock || faker.random.arrayElement(behavior);
  }
};
