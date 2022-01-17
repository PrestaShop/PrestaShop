const faker = require('faker');

const behavior = ['Deny orders', 'Allow orders', 'Default behavior'];

/**
 * Create new product to use on creation form on product page on BO
 * @class
 */
class ProductData {
  /**
   * Constructor for class ProductData
   * @param productToCreate {Object} Could be used to force the value of some members
   */
  constructor(productToCreate) {
    // Basic settings form
    /** @type {string} Name of the product */
    this.name = (productToCreate.name || faker.commerce.productName()).toUpperCase();

    /** @type {string} Type of the product */
    this.type = productToCreate.type;

    /** @type {?string} Cover image path for the product */
    this.coverImage = productToCreate.coverImage || null;

    /** @type {?string} Thumb image path for the product */
    this.thumbImage = productToCreate.thumbImage || null;

    /** @type {Object} Pack of products to add to the product */
    this.pack = productToCreate.pack || {
      demo_1: faker.random.number({min: 10, max: 100}),
      demo_2: faker.random.number({min: 10, max: 100}),
    };

    /** @type {boolean} Status of the product */
    this.status = productToCreate.status === undefined ? true : productToCreate.status;

    /** @type {string} Summary of the product */
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;

    /** @type {string} Description of the product */
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;

    /** @type {boolean} True to create product with combination */
    this.productHasCombinations = productToCreate.productHasCombinations || false;

    /** @type {string} Reference of the product */
    this.reference = productToCreate.reference || faker.random.alphaNumeric(7);

    /** @type {number} Quantity available of the product */
    this.quantity = productToCreate.quantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.quantity;

    /** @type {string} Price tax included of the product */
    this.price = productToCreate.price === undefined ? faker.random.number({min: 10, max: 20}) : productToCreate.price;

    /** @type {string} Tac rule to apply the product */
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';

    // Combinations form
    /** @type {Object|{color: Array<string>, size: Array<string>}} Combinations of the product */
    this.combinations = productToCreate.combinations || {
      color: ['White', 'Black'],
      size: ['S', 'M'],
    };

    // Pricing form
    this.productWithEcoTax = productToCreate.productWithEcoTax || false;

    /** @type {number} EcoTax tax included of the product */
    this.ecoTax = productToCreate.ecoTax === undefined
      ? faker.random.number({min: 10, max: 20})
      : productToCreate.ecoTax;

    this.productWithSpecificPrice = productToCreate.productWithSpecificPrice || false;
    /** @type {Object|{combinations: ?string, discount: ?number, startingAt: ?number}} Specific price of the product */
    this.specificPrice = productToCreate.specificPrice || {
      combinations: 'Size - S, Color - White',
      discount: faker.random.number({min: 10, max: 100}),
      startingAt: faker.random.number({min: 2, max: 5}),
    };

    // Quantities form
    /** @type {number} Minimum quantity to buy for the product */
    this.minimumQuantity = productToCreate.minimumQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.minimumQuantity;

    /** @type {string} Stock location of the product */
    this.stockLocation = productToCreate.stockLocation || 'stock 1';

    /** @type {number} Low stock level of the product */
    this.lowStockLevel = productToCreate.lowStockLevel === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.lowStockLevel;

    /** @type {string} Label to add if product is in stock */
    this.labelWhenInStock = productToCreate.labelWhenInStock || 'Label when in stock';

    /** @type {string} Label to add if product is out of stock */
    this.LabelWhenOutOfStock = productToCreate.LabelWhenOutOfStock || 'Label when out of stock';

    /** @type {string} Product behavior when it's out of stock */
    this.behaviourOutOfStock = productToCreate.behaviourOutOfStock || faker.random.arrayElement(behavior);
  }
}

module.exports = ProductData;
