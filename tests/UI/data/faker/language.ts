import LanguageCreator from '@data/types/language';

import {faker} from '@faker-js/faker';

/**
 * Create new language to use on creation form on language page on BO
 * @class
 */
export default class LanguageData {
  public readonly id: number;

  public readonly name: string;

  public readonly isoCode: string;

  public readonly languageCode: string;

  public readonly dateFormat: string;

  public readonly fullDateFormat: string;

  public readonly isRtl: boolean;

  public readonly enabled: boolean;

  public readonly flag: string;

  public readonly noPicture: string;

  /**
   * Constructor for class LanguageData
   * @param LanguageToCreate {LanguageCreator} Could be used to force the value of some members
   */
  constructor(LanguageToCreate: LanguageCreator = {}) {
    /** @type {string} ID of the language */
    this.id = LanguageToCreate.id || 0;

    /** @type {string} Name of the language */
    this.name = LanguageToCreate.name || `test_language_${faker.lorem.word()}`;

    /** @type {string} Iso code of the language */
    this.isoCode = LanguageToCreate.isoCode || 'en';

    /** @type {string} Language of the code */
    this.languageCode = LanguageToCreate.languageCode || this.isoCode;

    /** @type {string} Date format for the chosen language */
    this.dateFormat = LanguageToCreate.dateFormat || 'Y-m-d';

    /** @type {string} Full date format for the chosen language */
    this.fullDateFormat = LanguageToCreate.fullDateFormat || 'Y-m-d H:i:s';

    /** @type {boolean} True if it's a right to left language */
    this.isRtl = LanguageToCreate.isRtl === undefined ? false : LanguageToCreate.isRtl;

    /** @type {boolean} Status of the language */
    this.enabled = LanguageToCreate.enabled === undefined ? true : LanguageToCreate.enabled;

    /** @type {string} Language flag path */
    this.flag = LanguageToCreate.flag || `flag_${this.name}.png`;

    /** @type {string} Language no picture path */
    this.noPicture = LanguageToCreate.noPicture || `no_picture_${this.name}.png`;
  }
}
