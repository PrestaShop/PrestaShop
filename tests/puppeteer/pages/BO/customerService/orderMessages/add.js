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
    this.nameLangSpan = `${this.langDropdownDiv} span[data-locale='%LANG']`;
    this.nameInput = '#order_message_name_%ID';
    this.messageTextarea = '#order_message_message_%ID';
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
      this.page.waitForSelector(`${this.nameLangButton}[aria-expanded='true']`, {visible: true}),
    ]);
    await Promise.all([
      this.page.click(this.nameLangSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.nameLangButton}[aria-expanded='false']`, {visible: true}),
    ]);
  }

  /**
   *
   * @param orderMessageData
   * @return {Promise<textContent>}
   */
  async AddEditOrderMessage(orderMessageData) {
    // Change lang to 'en' than set inputs value
    await this.changeFormLang('en');
    await this.setValue(this.nameInput.replace('%ID', 1), orderMessageData.name);
    await this.setValue(this.messageTextarea.replace('%ID', 1), orderMessageData.message);
    // Change lang to 'fr' than set inputs value
    await this.changeFormLang('fr');
    await this.setValue(this.nameInput.replace('%ID', 2), orderMessageData.frName);
    await this.setValue(this.messageTextarea.replace('%ID', 2), orderMessageData.frMessage);
    // Save order message
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
