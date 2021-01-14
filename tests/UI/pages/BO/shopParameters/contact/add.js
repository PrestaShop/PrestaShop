require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddContact extends BOBasePage {
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
    this.enableSaveMessagesLabel = id => `label[for='contact_is_messages_saving_enabled_${id}']`;
    this.descriptionTextareaEN = '#contact_description_1';
    this.descriptionTextareaFR = '#contact_description_2';
    this.saveContactButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Change language for selectors
   * @param page
   * @param lang
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
   * @param page
   * @param contactData
   * @returns {Promise<string>}
   */
  async createEditContact(page, contactData) {
    await this.setValue(page, this.titleInputEN, contactData.title);
    await this.setValue(page, this.emailAddressInput, contactData.email);
    await this.setValue(page, this.descriptionTextareaEN, contactData.description);
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.titleInputFR, contactData.title);
    await this.setValue(page, this.descriptionTextareaFR, contactData.description);
    await page.click(this.enableSaveMessagesLabel(contactData.saveMessage ? 1 : 0));
    // Save Contact
    await this.clickAndWaitForNavigation(page, this.saveContactButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddContact();
