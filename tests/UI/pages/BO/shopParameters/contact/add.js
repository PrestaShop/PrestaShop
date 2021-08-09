require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add contact page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddContact extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add contact page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Contacts •';
    this.pageTitleEdit = 'Contacts •';

    // Selectors
    this.pageTitleLangButton = '#contact_title';
    this.pageTitleLangSpan = lang => `div.dropdown-menu[aria-labelledby='contact_title'] span[data-locale='${lang}']`;
    this.titleInputEN = '#contact_title_1';
    this.titleInputFR = '#contact_title_2';
    this.emailAddressInput = '#contact_email';
    this.enableSaveMessagesToggleInput = toggle => `#contact_is_messages_saving_enabled_${toggle}`;
    this.descriptionTextareaEN = '#contact_description_1';
    this.descriptionTextareaFR = '#contact_description_2';
    this.saveContactButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page, lang = 'en') {
    await Promise.all([
      page.click(this.pageTitleLangButton),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.pageTitleLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Fill form for add/edit contact
   * @param page {Page} Browser tab
   * @param contactData {ContactData} Data to set on contact form
   * @returns {Promise<string>}
   */
  async createEditContact(page, contactData) {
    await this.setValue(page, this.titleInputEN, contactData.title);
    await this.setValue(page, this.emailAddressInput, contactData.email);
    await this.setValue(page, this.descriptionTextareaEN, contactData.description);
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.titleInputFR, contactData.title);
    await this.setValue(page, this.descriptionTextareaFR, contactData.description);
    await page.check(this.enableSaveMessagesToggleInput(contactData.saveMessage ? 1 : 0));
    // Save Contact
    await this.clickAndWaitForNavigation(page, this.saveContactButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddContact();
