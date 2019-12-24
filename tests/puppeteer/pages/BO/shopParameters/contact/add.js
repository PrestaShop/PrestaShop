require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddContact extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Contacts •';
    this.pageTitleEdit = 'Contacts •';

    // Selectors
    this.titleInput = '#contact_title_1';
    this.emailAddressInput = '#contact_email';
    this.enableSaveMessageslabel = 'label[for=\'contact_is_messages_saving_enabled_%ID\']';
    this.descriptionTextarea = '#contact_description_1';
    this.saveContactButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit contact
   * @param contactData
   * @return {Promise<textContent>}
   */
  async createEditContact(contactData) {
    await this.setValue(this.titleInput, contactData.firstName);
    await this.setValue(this.emailAddressInput, contactData.email);
    await this.setValue(this.descriptionTextarea, contactData.description);
    if (contactData.saveMessage) await this.page.click(this.enableSaveMessageslabel.replace('%ID', '1'));
    // Save Contact
    await this.clickAndWaitForNavigation(this.saveContactButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
