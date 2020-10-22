const faker = require('faker');

module.exports = class Language {
  constructor(LanguageToCreate = {}) {
    this.name = LanguageToCreate.name || `test_language_${faker.lorem.word()}`;
    this.isoCode = LanguageToCreate.isoCode;
    this.languageCode = LanguageToCreate.languageCode || this.isoCode;
    this.dateFormat = LanguageToCreate.dateFormat || 'Y-m-d';
    this.fullDateFormat = LanguageToCreate.fullDateFormat || 'Y-m-d H:i:s';
    this.isRtl = LanguageToCreate.isRtl === undefined ? false : LanguageToCreate.isRtl;
    this.status = LanguageToCreate.status === undefined ? true : LanguageToCreate.status;
    this.flag = LanguageToCreate.flag || `flag_${this.name}.png`;
    this.noPicture = LanguageToCreate.noPicture || `no_picture_${this.name}.png`;
  }
};
