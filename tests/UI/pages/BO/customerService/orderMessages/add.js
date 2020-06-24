require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddOrderMessage extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Add new';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameLangButton = '#order_message_name';
    this.langDropdownDiv = 'div.dropdown-menu[aria-labelledby=\'order_message_name\']';
    this.nameLangSpan = lang => `${this.langDropdownDiv} span[data-locale='${lang}']`;
    this.nameInput = id => `#order_message_name_${id}`;
    this.messageTextarea = id => `#order_message_message_${id}`;
    this.saveButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Change form language
   * @param lang
   * @return {Promise<void>}
   */
  async changeFormLang(lang = 'en') {
    await Promise.all([
      this.page.click(this.nameLangButton),
      this.waitForVisibleSelector(`${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.nameLangSpan(lang)),
      this.waitForVisibleSelector(`${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Add/Edit order message
   * @param orderMessageData
   * @returns {Promise<string>}
   */
  async addEditOrderMessage(orderMessageData) {
    // Change lang to 'en' than set inputs value
    await this.changeFormLang('en');
    await this.setValue(this.nameInput(1), orderMessageData.name);
    await this.setValue(this.messageTextarea(1), orderMessageData.message);
    // Change lang to 'fr' than set inputs value
    await this.changeFormLang('fr');
    await this.setValue(this.nameInput(2), orderMessageData.frName);
    await this.setValue(this.messageTextarea(2), orderMessageData.frMessage);
    // Save order message
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
