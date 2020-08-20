const faker = require('faker');

module.exports = class ImageType {
  constructor(imageTypeToCreate = {}) {
    this.name = imageTypeToCreate.name || faker.lorem.word();
    this.width = imageTypeToCreate.width || faker.random.number({min: 100, max: 200});
    this.height = imageTypeToCreate.height || faker.random.number({min: 100, max: 200});

    this.productsStatus = imageTypeToCreate.productsStatus === undefined
      ? true : imageTypeToCreate.productsStatus;

    this.categoriesStatus = imageTypeToCreate.categoriesStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;

    this.manufacturersStatus = imageTypeToCreate.manufacturersStatus === undefined
      ? true : imageTypeToCreate.manufacturersStatus;

    this.suppliersStatus = imageTypeToCreate.suppliersStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;

    this.storesStatus = imageTypeToCreate.storesStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;
  }
};
