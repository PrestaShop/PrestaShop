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
    /** @member {string} Name of the language */
    this.name = LanguageToCreate.name || `test_language_${faker.lorem.word()}`;

    /** @member {string} Iso code of the language */
    this.isoCode = LanguageToCreate.isoCode;

    /** @member {string} Language of the code */
    this.languageCode = LanguageToCreate.languageCode || this.isoCode;

    /** @member {string} Date format for the chosen language */
    this.dateFormat = LanguageToCreate.dateFormat || 'Y-m-d';

    /** @member {string} Full date format for the chosen language */
    this.fullDateFormat = LanguageToCreate.fullDateFormat || 'Y-m-d H:i:s';

    /** @member {boolean} True if it's a right to left language */
    this.isRtl = LanguageToCreate.isRtl === undefined ? false : LanguageToCreate.isRtl;

    /** @member {boolean} Status of the language */
    this.enabled = LanguageToCreate.enabled === undefined ? true : LanguageToCreate.enabled;

    /** @member {string} Language flag path */
    this.flag = LanguageToCreate.flag || `flag_${this.name}.png`;

    /** @member {string} Language no picture path */
    this.noPicture = LanguageToCreate.noPicture || `no_picture_${this.name}.png`;
  }
}
module.exports = LanguageData;
