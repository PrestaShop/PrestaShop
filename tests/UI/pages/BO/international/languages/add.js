require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

/**
 * Add language page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class AddLanguage extends LocalizationBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add language page
   */
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
    this.isRtlToggleInput = toggle => `#language_is_rtl_${toggle}`;
    this.statusToggleInput = toggle => `#language_is_active_${toggle}`;
    this.saveButton = '#save-button';
  }

  /* Methods */

  /**
   * Create or edit language
   * @param page {Page} Browser tab
   * @param languageData {LanguageData} Data to set on add/edit language form
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
    await page.check(this.isRtlToggleInput(languageData.isRtl ? 1 : 0));
    await page.check(this.statusToggleInput(languageData.enabled ? 1 : 0));

    // Save and return result
    await this.clickAndWaitForNavigation(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddLanguage();
