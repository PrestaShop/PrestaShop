// Import data
import TitleCreator from '@data/types/title';

import {faker} from '@faker-js/faker';

const genders: string[] = ['Male', 'Female', 'Neutral'];

/**
 * Create new title to use on title form on BO
 * @class
 */
export default class TitleData {
  public readonly id: number;

  public readonly name: string;

  public readonly frName: string;

  public readonly gender: string;

  public readonly imageName: string;

  public readonly imageWidth: number;

  public readonly imageHeight: number;

  /**
   * Constructor for class TitleData
   * @param titleToCreate {TitleCreator} Could be used to force the value of some members
   */
  constructor(titleToCreate: TitleCreator = {}) {
    /** @type {number} ID of the title */
    this.id = titleToCreate.id || 0;

    // Title name should contain at most 20 characters
    /** @type {string} Name of the title */
    this.name = titleToCreate.name || (faker.lorem.word()).substring(0, 19).trim();

    /** @type {string} French name of the title */
    this.frName = titleToCreate.frName || this.name;

    /** @type {string} Gender type of the title */
    this.gender = titleToCreate.gender || faker.helpers.arrayElement(genders);

    /** @type {string} Name of the image to add to the title */
    this.imageName = titleToCreate.imageName || faker.system.commonFileName('png');

    /** @type {number} Width of the image */
    this.imageWidth = titleToCreate.imageWidth || 16;

    /** @type {number} Height of the image */
    this.imageHeight = titleToCreate.imageHeight || 16;
  }
}
