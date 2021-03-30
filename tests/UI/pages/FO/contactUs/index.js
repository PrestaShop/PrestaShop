require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class ContactUs extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Contact us';
    this.validationMessage = 'Your message has been successfully sent to our team.';

    // Left column selectors
    this.emailUsLink = '#left-column a';

    // Form selectors
    this.subjectSelect = '#content select[name=\'id_contact\']';
    this.emailAddressInput = '#content input[name=\'from\']';
    this.attachmentLabel = '#filestyle-0';
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
   * @param page
   * @returns {Promise<string>}
   */
  getEmailUsLink(page) {
    return this.getAttributeContent(page, this.emailUsLink, 'href');
  }

  /**
   * Send message
   * @param page
   * @param contactUsData
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
}

module.exports = new ContactUs();
