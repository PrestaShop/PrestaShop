const faker = require('faker');

/**
 * Create new image type to use on creation form on image type page on BO
 * @class
 */
class ImageTypeData {
  /**
   * Constructor for class ImageTypeData
   * @param imageTypeToCreate {Object} Could be used to force the value of some members
   */
  constructor(imageTypeToCreate = {}) {
    /** @member {string} Name of the image type */
    this.name = imageTypeToCreate.name || faker.lorem.word();

    /** @member {number} Image width */
    this.width = imageTypeToCreate.width || faker.random.number({min: 100, max: 200});

    /** @member {number} Image height */
    this.height = imageTypeToCreate.height || faker.random.number({min: 100, max: 200});

    /** @member {boolean} To activate type for products */
    this.productsStatus = imageTypeToCreate.productsStatus === undefined
      ? true : imageTypeToCreate.productsStatus;

    /** @member {boolean} To activate type for categories */
    this.categoriesStatus = imageTypeToCreate.categoriesStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;

    /** @member {boolean} To activate type for manufacturers */
    this.manufacturersStatus = imageTypeToCreate.manufacturersStatus === undefined
      ? true : imageTypeToCreate.manufacturersStatus;

    /** @member {boolean} To activate type for suppliers */
    this.suppliersStatus = imageTypeToCreate.suppliersStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;

    /** @member {boolean} To activate type for stores */
    this.storesStatus = imageTypeToCreate.storesStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;
  }
}

module.exports = ImageTypeData;
