import {
  ProductAttributes, ProductCombination,
  ProductCreator,
  ProductCustomization,
  ProductPackItem,
  ProductSpecificPrice,
  ProductFeatures,
  ProductFiles,
  ProductCustomizations,
} from '@data/types/product';

import {faker} from '@faker-js/faker';

const behavior: string[] = ['Deny orders', 'Allow orders', 'Default behavior'];

/**
 * Create new product to use on creation form on product page on BO
 * @class
 */
export default class ProductData {
  public id: number;

  public name: string;

  public nameFR: string;

  public defaultImage: string | null;

  public coverImage: string | null;

  public thumbImage: string | null;

  public thumbImageFR: string | null;

  public category: string;

  public type: string;

  public status: boolean;

  public applyChangesToAllStores: boolean;

  public summary: string;

  public description: string;

  public reference: string;

  public mpn: string | null;

  public upc: string | null;

  public ean13: string | null;

  public isbn: string | null;

  public features: ProductFeatures[];

  public files: ProductFiles[];

  public displayCondition: boolean;

  public condition: string;

  public quantity: number;

  public tax: number;

  public price: number;

  public retailPrice: number;

  public priceTaxExcluded: number;

  public finalPrice: number;

  public onSale: boolean;

  public productHasCombinations: boolean;

  public attributes: ProductAttributes[];

  public pack: ProductPackItem[];

  public taxRule: string;

  public ecoTax: number;

  public specificPrice: ProductSpecificPrice;

  public minimumQuantity: number;

  public stockLocation: string;

  public lowStockLevel: number;

  public labelWhenInStock: string;

  public labelWhenOutOfStock: string;

  public behaviourOutOfStock: string;

  public customization: ProductCustomization;

  public customizations: ProductCustomizations[];

  public downloadFile: boolean;

  public fileName: string;

  public allowedDownload: number;

  public expirationDate: string | null;

  public numberOfDays: number | null;

  public metaTitle: string | null;

  public metaDescription: string | null;

  public friendlyUrl: string | null;

  public combinations: ProductCombination[];

  public packageDimensionWidth: number;

  public packageDimensionHeight: number;

  public packageDimensionDepth: number;

  public packageDimensionWeight: number;

  public deliveryTime: string;

  /**
   * Constructor for class ProductData
   * @param productToCreate {Object} Could be used to force the value of some members
   */
  constructor(productToCreate: ProductCreator = {}) {
    /** @type {number} ID of the product */
    this.id = productToCreate.id || 0;

    /** @type {string} Name of the product */
    this.name = productToCreate.name || faker.commerce.productName();

    /** @type {string} Name of the product */
    this.nameFR = productToCreate.nameFR || this.name;

    /** @type {string|null} Default image path for the product */
    this.defaultImage = productToCreate.defaultImage || null;

    /** @type {string|null} Cover image path for the product */
    this.coverImage = productToCreate.coverImage || null;

    /** @type {string|null} Thumb image path for the product */
    this.thumbImage = productToCreate.thumbImage || null;

    /** @type {string|null} Thumb image path for the product */
    this.thumbImageFR = productToCreate.thumbImageFR || this.thumbImage;

    /** @type {string} Category for the product */
    this.category = productToCreate.category || 'Root';

    /** @type {string} Type of the product */
    this.type = productToCreate.type || 'Standard product';

    /** @type {boolean} Status of the product */
    this.status = productToCreate.status === undefined ? true : productToCreate.status;

    /** @type {boolean} Apply changes to all stores */
    this.applyChangesToAllStores = productToCreate.applyChangesToAllStores || false;

    /** @type {string} Summary of the product */
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;

    /** @type {string} Description of the product */
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;

    /** @type {string} Reference of the product */
    this.reference = productToCreate.reference || faker.string.alphanumeric(7);

    /** @type {string|null} mpn of the product */
    this.mpn = productToCreate.mpn || null;

    /** @type {string|null} upc of the product */
    this.upc = productToCreate.upc || null;

    /** @type {string|null} ean13 of the product */
    this.ean13 = productToCreate.ean13 || null;

    /** @type {string|null} isbn of the product */
    this.isbn = productToCreate.isbn || null;

    /** @type {number} Quantity available of the product */
    this.quantity = productToCreate.quantity === undefined
      ? faker.number.int({min: 1, max: 9})
      : productToCreate.quantity;

    /** @type {number} Tax for the product */
    this.tax = productToCreate.tax === undefined
      ? faker.number.int({min: 1, max: 100})
      : productToCreate.tax;

    /** @type {number} Price tax included of the product */
    this.price = productToCreate.price === undefined
      ? faker.number.int({min: 10, max: 20}) : productToCreate.price;

    /** @type {number} Retail price of the product */
    this.retailPrice = productToCreate.retailPrice === undefined
      ? this.price : productToCreate.retailPrice;

    /** @type {number} Price tax excluded of the product */
    this.priceTaxExcluded = productToCreate.priceTaxExcluded || (this.price * 100) / (100 + this.tax);

    /** @type {number} Final Price of the product */
    this.finalPrice = productToCreate.finalPrice || this.price;

    /** @type {boolean} True to enable on sale flag */
    this.onSale = productToCreate.onSale || false;

    /** @type {boolean} True to create product with combination */
    this.productHasCombinations = productToCreate.productHasCombinations || false;

    /** @type {Object|{color: Array<string>, size: Array<string>}} Combinations of the product */
    this.attributes = productToCreate.attributes || [
      {
        name: 'color',
        values: ['White', 'Black'],
      },
      {
        name: 'size',
        values: ['S', 'M'],
      },
    ];

    /** @type {ProductFeatures[]} Features of the product */
    this.features = productToCreate.features || [
      {
        featureName: 'Composition',
        preDefinedValue: 'Cotton',
      },
    ];

    /** @type {ProductFiles[]} Files attached to the product in details tab*/
    this.files = productToCreate.files || [
      {
        fileName: 'test',
        description: 'test',
        file: 'test.txt',
      },
    ];

    /** @type {boolean} Boolean to choose if we need to display condition or not */
    this.displayCondition = productToCreate.displayCondition || false;

    /** @type {string} Condition to choose */
    this.condition = productToCreate.condition || 'Use';

    /** @type {ProductPackItem[]} Pack of products to add to the product */
    this.pack = productToCreate.pack || [
      {
        reference: 'demo_1',
        quantity: faker.number.int({min: 10, max: 100}),
      },
      {
        reference: 'demo_2',
        quantity: faker.number.int({min: 10, max: 100}),
      },
    ];

    /** @type {string} Tac rule to apply the product */
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';

    /** @type {number} EcoTax tax included of the product */
    this.ecoTax = productToCreate.ecoTax === undefined
      ? faker.number.int({min: 1, max: 5})
      : productToCreate.ecoTax;

    /** @type {ProductSpecificPrice} Specific price of the product */
    this.specificPrice = productToCreate.specificPrice || {
      attributes: 'Size - S, Color - White',
      discount: faker.number.int({min: 10, max: 100}),
      startingAt: faker.number.int({min: 2, max: 5}),
      reductionType: '',
    };

    /** @type {number} Minimum quantity to buy for the product */
    this.minimumQuantity = productToCreate.minimumQuantity || 1;

    /** @type {string} Stock location of the product */
    this.stockLocation = productToCreate.stockLocation || 'stock 1';

    /** @type {number} Low stock level of the product */
    this.lowStockLevel = productToCreate.lowStockLevel === undefined
      ? faker.number.int({min: 1, max: 9})
      : productToCreate.lowStockLevel;

    /** @type {string} Label to add if product is in stock */
    this.labelWhenInStock = productToCreate.labelWhenInStock || 'Label when in stock';

    /** @type {string} Label to add if product is out of stock */
    this.labelWhenOutOfStock = productToCreate.labelWhenOutOfStock || 'Label when out of stock';

    /** @type {string} Product behavior when it's out of stock */
    this.behaviourOutOfStock = productToCreate.behaviourOutOfStock || faker.helpers.arrayElement(behavior);

    /** @type {ProductCustomization} Customized value of the product */
    this.customization = productToCreate.customization || {
      label: 'Type your text here',
      type: 'Text',
      required: true,
    };

    /** @type {ProductCustomization} Customized value of the product */
    this.customizations = productToCreate.customizations || [
      {
        label: 'Type your text here',
        type: 'Text',
        required: true,
      }];

    /** @type {boolean} True to download file */
    this.downloadFile = productToCreate.downloadFile || false;

    /** @type {string} File name to put it in virtual tab */
    this.fileName = productToCreate.fileName || 'virtual.jpg';

    /** @type {number} Number of allowed downloads */
    this.allowedDownload = productToCreate.allowedDownload || faker.number.int({min: 1, max: 20});

    /** @type {string} Expiration date */
    this.expirationDate = productToCreate.expirationDate || null;

    /** @type {number} Number of days limit */
    this.numberOfDays = productToCreate.numberOfDays || null;

    /** @type {string} Meta title */
    this.metaTitle = productToCreate.metaTitle || null;

    /** @type {string} Meta description */
    this.metaDescription = productToCreate.metaDescription || null;

    /** @type {string} Friendly URL */
    this.friendlyUrl = productToCreate.friendlyUrl || null;

    /** @type {number} Weight of the package */
    this.packageDimensionWeight = productToCreate.packageDimensionWeight || faker.number.int({min: 1, max: 20});

    /** @type {number} Width of the package */
    this.packageDimensionWidth = productToCreate.packageDimensionWidth || faker.number.int({min: 1, max: 20});

    /** @type {number} Height of the package */
    this.packageDimensionHeight = productToCreate.packageDimensionHeight || faker.number.int({min: 1, max: 20});

    /** @type {number} Depth of the package */
    this.packageDimensionDepth = productToCreate.packageDimensionDepth || faker.number.int({min: 1, max: 20});

    /** @type {string} Delivery time */
    this.deliveryTime = productToCreate.deliveryTime || 'Default delivery time';

    /** @type {ProductCombination[]} */
    this.combinations = productToCreate.combinations || [];
  }
}
