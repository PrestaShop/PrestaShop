require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddContact extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Contacts •';
    this.pageTitleEdit = 'Contacts •';

    // Selectors
    this.pageTitleLangButton = '#contact_title';
    this.pageTitleLangSpan = 'div.dropdown-menu[aria-labelledby=\'contact_title\'] span[data-locale=\'%LANG\']';
    this.titleInputEN = '#contact_title_1';
    this.titleInputFR = '#contact_title_2';
    this.emailAddressInput = '#contact_email';
    this.enableSaveMessageslabel = 'label[for=\'contact_is_messages_saving_enabled_%ID\']';
    this.descriptionTextareaEN = '#contact_description_1';
    this.descriptionTextareaFR = '#contact_description_2';
    this.saveContactButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Change language for selectors
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(lang = 'en') {
    await Promise.all([
      this.page.click(this.pageTitleLangButton),
      this.page.waitForSelector(`${this.pageTitleLangButton}[aria-expanded='true']`, {visible: true}),
    ]);
    await Promise.all([
      this.page.click(this.pageTitleLangSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.pageTitleLangButton}[aria-expanded='false']`, {visible: true}),
    ]);
  }

  /**
   * Fill form for add/edit contact
   * @param contactData
   * @return {Promise<textContent>}
   */
  async createEditContact(contactData) {
    await this.setValue(this.titleInputEN, contactData.title);
    await this.setValue(this.emailAddressInput, contactData.email);
    await this.setValue(this.descriptionTextareaEN, contactData.description);
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.titleInputFR, contactData.title);
    await this.setValue(this.descriptionTextareaFR, contactData.description);
    await this.page.click(this.enableSaveMessageslabel.replace('%ID', contactData.saveMessage ? 1 : 0));
    // Save Contact
    await this.clickAndWaitForNavigation(this.saveContactButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
