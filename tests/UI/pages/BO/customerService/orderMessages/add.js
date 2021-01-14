require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddOrderMessage extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @param lang
   * @return {Promise<void>}
   */
  async changeFormLang(page, lang = 'en') {
    await Promise.all([
      page.click(this.nameLangButton),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.nameLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Add/Edit order message
   * @param page
   * @param orderMessageData
   * @returns {Promise<string>}
   */
  async addEditOrderMessage(page, orderMessageData) {
    // Change lang to 'en' than set inputs value
    await this.changeFormLang(page, 'en');
    await this.setValue(page, this.nameInput(1), orderMessageData.name);
    await this.setValue(page, this.messageTextarea(1), orderMessageData.message);
    // Change lang to 'fr' than set inputs value
    await this.changeFormLang(page, 'fr');
    await this.setValue(page, this.nameInput(2), orderMessageData.frName);
    await this.setValue(page, this.messageTextarea(2), orderMessageData.frMessage);
    // Save order message
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddOrderMessage();
