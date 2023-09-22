import {faker} from '@faker-js/faker';
import {ImageTypeCreator} from '@data/types/imageType';

/**
 * Create new image type to use on creation form on image type page on BO
 * @class
 */
export default class ImageTypeData {
  public readonly id: number;

  public readonly name: string;

  public readonly width: number;

  public readonly height: number;

  public readonly productsStatus: boolean;

  public readonly categoriesStatus: boolean;

  public readonly manufacturersStatus: boolean;

  public readonly suppliersStatus: boolean;

  public readonly storesStatus: boolean;

  /**
   * Constructor for class ImageTypeData
   * @param imageTypeToCreate {ImageTypeCreator} Could be used to force the value of some members
   */
  constructor(imageTypeToCreate: ImageTypeCreator = {}) {
    /** @type {number} ID of the image type */
    this.id = imageTypeToCreate.id || 0;

    /** @type {string} Name of the image type */
    this.name = imageTypeToCreate.name || faker.lorem.word();

    /** @type {number} Image width */
    this.width = imageTypeToCreate.width || faker.number.int({min: 100, max: 200});

    /** @type {number} Image height */
    this.height = imageTypeToCreate.height || faker.number.int({min: 100, max: 200});

    /** @type {boolean} To activate type for products */
    this.productsStatus = imageTypeToCreate.productsStatus === undefined
      ? true : imageTypeToCreate.productsStatus;

    /** @type {boolean} To activate type for categories */
    this.categoriesStatus = imageTypeToCreate.categoriesStatus === undefined
      ? true : imageTypeToCreate.categoriesStatus;

    /** @type {boolean} To activate type for manufacturers */
    this.manufacturersStatus = imageTypeToCreate.manufacturersStatus === undefined
      ? true : imageTypeToCreate.manufacturersStatus;

    /** @type {boolean} To activate type for suppliers */
    this.suppliersStatus = imageTypeToCreate.suppliersStatus === undefined
      ? true : imageTypeToCreate.suppliersStatus;

    /** @type {boolean} To activate type for stores */
    this.storesStatus = imageTypeToCreate.storesStatus === undefined
      ? true : imageTypeToCreate.storesStatus;
  }
}
