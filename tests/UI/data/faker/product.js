const {faker} = require('@faker-js/faker');

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
    /** @type {string} Name of the product */
    this.name = (productToCreate.name || faker.commerce.productName()).toUpperCase();

    /** @type {?string} Cover image path for the product */
    this.coverImage = productToCreate.coverImage || null;

    /** @type {?string} Thumb image path for the product */
    this.thumbImage = productToCreate.thumbImage || null;

    /** @type {string} Type of the product */
    this.type = productToCreate.type;

    /** @type {boolean} Status of the product */
    this.status = productToCreate.status === undefined ? true : productToCreate.status;

    /** @type {string} Summary of the product */
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;

    /** @type {string} Description of the product */
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;

    /** @type {string} Reference of the product */
    this.reference = productToCreate.reference || faker.random.alphaNumeric(7);

    /** @type {number} Quantity available of the product */
    this.quantity = productToCreate.quantity === undefined
      ? faker.datatype.number({min: 1, max: 9})
      : productToCreate.quantity;

    /** @type {number} Tax for the product */
    this.tax = productToCreate.tax === undefined
      ? faker.datatype.number({min: 1, max: 100})
      : productToCreate.tax;

    /** @type {string} Price tax included of the product */
    this.price = productToCreate.price === undefined
      ? faker.datatype.number({min: 10, max: 20}) : productToCreate.price;

    /** @type {string} Price tax excluded of the product */
    this.priceTaxExcluded = productToCreate.priceTaxExcluded || (this.price * 100) / (100 + this.tax);

    /** @type {boolean} True to create product with combination */
    this.productHasCombinations = productToCreate.productHasCombinations || false;

    /** @type {Object|{color: Array<string>, size: Array<string>}} Combinations of the product */
    this.combinations = productToCreate.combinations || {
      color: ['White', 'Black'],
      size: ['S', 'M'],
    };

    /** @type {Object} Pack of products to add to the product */
    this.pack = productToCreate.pack || {
      demo_1: faker.datatype.number({min: 10, max: 100}),
      demo_2: faker.datatype.number({min: 10, max: 100}),
    };

    /** @type {string} Tac rule to apply the product */
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';

    /** @type {number} EcoTax tax included of the product */
    this.ecoTax = productToCreate.ecoTax === undefined
      ? faker.datatype.number({min: 1, max: 5})
      : productToCreate.ecoTax;

    /** @type {Object|{combinations: ?string, discount: ?number, startingAt: ?number}} Specific price of the product */
    this.specificPrice = productToCreate.specificPrice || {
      combinations: 'Size - S, Color - White',
      discount: faker.datatype.number({min: 10, max: 100}),
      startingAt: faker.datatype.number({min: 2, max: 5}),
    };

    /** @type {number} Minimum quantity to buy for the product */
    this.minimumQuantity = productToCreate.minimumQuantity === undefined
      ? faker.datatype.number({min: 1, max: 9})
      : productToCreate.minimumQuantity;

    /** @type {string} Stock location of the product */
    this.stockLocation = productToCreate.stockLocation || 'stock 1';

    /** @type {number} Low stock level of the product */
    this.lowStockLevel = productToCreate.lowStockLevel === undefined
      ? faker.datatype.number({min: 1, max: 9})
      : productToCreate.lowStockLevel;

    /** @type {string} Label to add if product is in stock */
    this.labelWhenInStock = productToCreate.labelWhenInStock || 'Label when in stock';

    /** @type {string} Label to add if product is out of stock */
    this.LabelWhenOutOfStock = productToCreate.LabelWhenOutOfStock || 'Label when out of stock';

    /** @type {string} Product behavior when it's out of stock */
    this.behaviourOutOfStock = productToCreate.behaviourOutOfStock || faker.helpers.arrayElement(behavior);

    /** @type {Object|{label: ?string, type: ?string, required: ?boolean}} Customized value of the product */
    this.customization = productToCreate.customization || {
      label: 'Type your text here',
      type: 'Text',
      required: true,
    };
  }
}

module.exports = ProductData;
