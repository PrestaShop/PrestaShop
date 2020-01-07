require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddLanguage extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Add new •';
    this.pageEditTitle = 'Edit:';

    // Selectors
    this.nameInput = '#language_name';
    this.isoCodeInput = '#language_iso_code';
    this.languageCodeInput = '#language_tag_ietf';
    this.dateFormatInput = '#language_short_date_format';
    this.fullDataFormatInput = '#language_full_date_format';
    this.flagInput = '#language_flag_image';
    this.noPictureInput = '#language_no_picture_image';
    this.isRtlSwitch = 'label[for=\'language_is_rtl_%ID\']';
    this.statusSwitch = 'label[for=\'language_is_active_%ID\']';
    this.saveButton = 'div.card-footer button';
  }

  /* Methods */

  /**
   * Create or edit language
   * @param languageData
   * @return {Promise<string>}
   */
  async createEditLanguage(languageData) {
    // Set input text
    await this.setValue(this.nameInput, languageData.name);
    await this.setValue(this.isoCodeInput, languageData.isoCode);
    await this.setValue(this.languageCodeInput, languageData.languageCode);
    await this.setValue(this.dateFormatInput, languageData.dateFormat);
    await this.setValue(this.fullDataFormatInput, languageData.fullDateFormat);
    // Add images
    await this.generateAndUploadImage(this.flagInput, languageData.flag);
    await this.generateAndUploadImage(this.noPictureInput, languageData.noPicture);
    // Add switch
    await this.page.click(this.isRtlSwitch.replace('%ID', languageData.isRtl ? 1 : 0));
    await this.page.click(this.statusSwitch.replace('%ID', languageData.status ? 1 : 0));
    // Save and return result
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
