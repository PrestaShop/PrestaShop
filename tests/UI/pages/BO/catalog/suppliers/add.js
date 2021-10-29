require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add supplier page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddSupplier extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add supplier page
   */
  constructor() {
    super();

    this.pageTitle = 'Add new • ';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#supplier_name';
    this.descriptionDiv = '#supplier_description';
    this.descriptionLangNavItemLink = lang => `${this.descriptionDiv} ul li a[data-locale='${lang}']`;
    this.descriptionIFrame = id => `#supplier_description_${id}_ifr`;
    this.homePhoneInput = '#supplier_phone';
    this.mobilePhoneInput = '#supplier_mobile_phone';
    this.addressInput = '#supplier_address';
    this.secondaryAddressInput = '#supplier_address2';
    this.postalCodeInput = '#supplier_post_code';
    this.cityInput = '#supplier_city';
    this.countryInput = '#supplier_id_country';
    this.stateInput = '#supplier_id_state';
    this.logoFileInput = '#supplier_logo';
    this.metaTitleLangButton = '#supplier_meta_title';
    this.metaTitleLangSpan = lang => 'div.dropdown-menu[aria-labelledby=\'supplier_meta_title\']'
      + ` span[data-locale='${lang}']`;
    this.metaTitleInput = id => `#supplier_meta_title_${id}`;
    this.metaDescriptionTextarea = id => `#supplier_meta_description_${id}`;
    this.metaKeywordsInput = id => `#supplier_meta_keyword_${id}-tokenfield`;
    this.statusToggleInput = toggle => `#supplier_is_enabled_${toggle}`;

    // Selectors for Meta keywords
    this.taggableFieldDiv = lang => `div.input-group div.js-locale-${lang}`;
    this.deleteKeywordLink = lang => `${this.taggableFieldDiv(lang)} a.close`;
    this.saveButton = '.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Create or edit Supplier
   * @param page {Page} Browser tab
   * @param supplierData {SupplierData} Data to set on new/edit supplier form
   * @return {Promise<void>}
   */
  async createEditSupplier(page, supplierData) {
    // Fill Name
    await this.setValue(page, this.nameInput, supplierData.name);

    // Fill Address information
    await this.setValue(page, this.homePhoneInput, supplierData.homePhone);
    await this.setValue(page, this.mobilePhoneInput, supplierData.mobilePhone);
    await this.setValue(page, this.addressInput, supplierData.address);
    await this.setValue(page, this.secondaryAddressInput, supplierData.secondaryAddress);
    await this.setValue(page, this.postalCodeInput, supplierData.postalCode);
    await this.setValue(page, this.cityInput, supplierData.city);
    await this.setValue(page, this.countryInput, supplierData.country);

    // Add logo
    await this.uploadFile(page, this.logoFileInput, supplierData.logo);

    // Fill Description, meta title, meta description and meta keywords in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(1), supplierData.description);
    await this.setValue(page, this.metaTitleInput(1), supplierData.metaTitle);
    await this.setValue(page, this.metaDescriptionTextarea(1), supplierData.metaDescription);

    // delete Keywords and other new ones
    await this.deleteKeywords(page, 'en');
    await this.addKeywords(page, supplierData.metaKeywords, 1);

    // Fill Description, meta title, meta description and meta keywords in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(2), supplierData.descriptionFr);
    await this.setValue(page, this.metaTitleInput(2), supplierData.metaTitleFr);
    await this.setValue(page, this.metaDescriptionTextarea(2), supplierData.metaDescriptionFr);

    // delete Keywords and other new ones
    await this.deleteKeywords(page, 'fr');
    await this.addKeywords(page, supplierData.metaKeywords, 2);

    // Set status value
    await page.check(this.statusToggleInput(supplierData.enabled ? 1 : 0));

    // Save Supplier
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all keywords
   * @param page {Page} Browser tab
   * @param lang {string} To specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(page, lang = 'en') {
    const closeButtons = await page.$$(this.deleteKeywordLink(lang));

    /* eslint-disable no-restricted-syntax */
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Add keywords
   * @param page {Page} Browser tab
   * @param keywords {array} Array of keywords
   * @param idLang {number} To choose which lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(page, keywords, idLang = 1) {
    /* eslint-disable no-restricted-syntax */
    for (const keyword of keywords) {
      await page.type(this.metaKeywordsInput(idLang), keyword);
      await page.keyboard.press('Enter');
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Change language for description and meta selectors
   * @param page {Page} Browser tab
   * @param lang {string} To choose which language ('en' or 'fr')
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page, lang = 'en') {
    // Change language for Description input
    await Promise.all([
      page.click(this.descriptionLangNavItemLink(lang)),
      this.waitForVisibleSelector(page, `${this.descriptionLangNavItemLink(lang)}.active`),
    ]);

    // Change language for meta selectors
    await Promise.all([
      page.click(this.metaTitleLangButton),
      this.waitForVisibleSelector(page, `${this.metaTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.metaTitleLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.metaTitleLangButton}[aria-expanded='false']`),
    ]);
  }
}

module.exports = new AddSupplier();
