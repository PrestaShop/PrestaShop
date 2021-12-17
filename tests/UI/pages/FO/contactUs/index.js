require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Contact us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class ContactUs extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on contact us page
   */
  constructor() {
    super();

    this.pageTitle = 'Contact us';
    this.validationMessage = 'Your message has been successfully sent to our team.';

    // Left column selectors
    this.emailUsLink = '#left-column a';

    // Form selectors
    this.subjectSelect = '#content select[name=\'id_contact\']';
    this.emailAddressInput = '#content input[name=\'from\']';
    this.attachmentLabel = '#file-upload';
    this.orderReferenceSelect = 'select[name=id_order]';
    this.messageTextarea = '#content textarea[name=\'message\']';
    this.sendButton = '#content input[name=\'submitMessage\']';
    this.alertSuccessDiv = '#content div.alert-success';
  }

  /*
  Methods
   */
  /**
   * Get email us link href
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getEmailUsLink(page) {
    return this.getAttributeContent(page, this.emailUsLink, 'href');
  }

  /**
   * Send message
   * @param page {Page} Browser tab
   * @param contactUsData {object} The data for fill the form
   * @param file {string|null} The path of the file to upload
   * @returns {Promise<string>}
   */
  async sendMessage(page, contactUsData, file = null) {
    await this.selectByVisibleText(page, this.subjectSelect, contactUsData.subject);
    await this.setValue(page, this.emailAddressInput, contactUsData.emailAddress);

    if (file) {
      await this.uploadFile(page, this.attachmentLabel, file);
    }

    if (contactUsData.reference) {
      await this.selectByVisibleText(page, this.orderReferenceSelect, contactUsData.reference);
    }

    await this.setValue(page, this.messageTextarea, contactUsData.message);
    await page.click(this.sendButton);

    return this.getTextContent(page, this.alertSuccessDiv);
  }

  /**
   * Get and return the content of the email input
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getEmailFieldValue(page) {
    return this.getAttributeContent(page, this.emailAddressInput, 'value');
  }

  /**
   * Check if attachment input is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAttachmentInputVisible(page) {
    return this.elementVisible(page, this.attachmentLabel, 1000);
  }
}

module.exports = new ContactUs();
