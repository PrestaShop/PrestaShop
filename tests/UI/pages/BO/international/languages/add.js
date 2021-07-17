require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddLanguage extends BOBasePage {
  constructor() {
    super();

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
    this.isRtlSwitch = id => `label[for='language_is_rtl_${id}']`;
    this.statusSwitch = id => `label[for='language_is_active_${id}']`;
    this.saveButton = 'div.card-footer button';
  }

  /* Methods */

  /**
   * Create or edit language
   * @param page
   * @param languageData
   * @return {Promise<string>}
   */
  async createEditLanguage(page, languageData) {
    // Set input text
    await this.setValue(page, this.nameInput, languageData.name);
    await this.setValue(page, this.isoCodeInput, languageData.isoCode);
    await this.setValue(page, this.languageCodeInput, languageData.languageCode);
    await this.setValue(page, this.dateFormatInput, languageData.dateFormat);
    await this.setValue(page, this.fullDataFormatInput, languageData.fullDateFormat);

    // Add images
    await this.uploadFile(page, this.flagInput, languageData.flag);
    await this.uploadFile(page, this.noPictureInput, languageData.noPicture);

    // Set rtl and status
    await page.click(this.isRtlSwitch(languageData.isRtl ? 1 : 0));
    await page.click(this.statusSwitch(languageData.enabled ? 1 : 0));

    // Save and return result
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddLanguage();
