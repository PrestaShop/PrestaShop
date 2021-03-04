require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class ContactUs extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Contact us';
    this.validationMessage = 'Your message has been successfully sent to our team.';

    // Selectors
    this.subjectSelect = '#content select[name=\'id_contact\']';
    this.emailAddressInput = '#content input[name=\'from\']';
    this.attachmentLabel = '#filestyle-0';
    this.messageTextarea = '#content textarea[name=\'message\']';
    this.sendButton = '#content input[name=\'submitMessage\']';
    this.alertSuccessDiv = '#content div.alert-success';
  }

  /*
  Methods
   */
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
    await this.setValue(page, this.messageTextarea, contactUsData.message);
    await page.click(this.sendButton);

    return this.getTextContent(page, this.alertSuccessDiv);
  }
}

module.exports = new ContactUs();
