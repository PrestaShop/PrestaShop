const faker = require('faker');

/**
 * Create new language to use on creation form on language page on BO
 * @class
 */
class LanguageData {
  /**
   * Constructor for class LanguageData
   * @param LanguageToCreate {Object} Could be used to force the value of some members
   */
  constructor(LanguageToCreate = {}) {
    /** @type {string} Name of the language */
    this.name = LanguageToCreate.name || `test_language_${faker.lorem.word()}`;

    /** @type {string} Iso code of the language */
    this.isoCode = LanguageToCreate.isoCode;

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
module.exports = LanguageData;
