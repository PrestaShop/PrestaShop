import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import type LanguageData from '@data/faker/language';

import type {Page} from 'playwright';

/**
 * Add language page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class AddLanguage extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly pageEditTitle: string;

  private readonly nameInput: string;

  private readonly isoCodeInput: string;

  private readonly languageCodeInput: string;

  private readonly dateFormatInput: string;

  private readonly fullDataFormatInput: string;

  private readonly flagInput: string;

  private readonly noPictureInput: string;

  private readonly isRtlToggleInput: (toggle: number) => string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add language page
   */
  constructor() {
    super();

    this.pageTitle = `New language • ${global.INSTALL.SHOP_NAME}`;
    this.pageEditTitle = 'Editing language';

    // Selectors
    this.nameInput = '#language_name';
    this.isoCodeInput = '#language_iso_code';
    this.languageCodeInput = '#language_tag_ietf';
    this.dateFormatInput = '#language_short_date_format';
    this.fullDataFormatInput = '#language_full_date_format';
    this.flagInput = '#language_flag_image';
    this.noPictureInput = '#language_no_picture_image';
    this.isRtlToggleInput = (toggle: number) => `#language_is_rtl_${toggle}`;
    this.statusToggleInput = (toggle: number) => `#language_is_active_${toggle}`;
    this.saveButton = '#save-button';
  }

  /* Methods */

  /**
   * Create or edit language
   * @param page {Page} Browser tab
   * @param languageData {LanguageData} Data to set on add/edit language form
   * @return {Promise<string>}
   */
  async createEditLanguage(page: Page, languageData: LanguageData): Promise<string> {
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
    await this.setChecked(page, this.isRtlToggleInput(languageData.isRtl ? 1 : 0));
    await this.setChecked(page, this.statusToggleInput(languageData.enabled ? 1 : 0));

    // Save and return result
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddLanguage();
