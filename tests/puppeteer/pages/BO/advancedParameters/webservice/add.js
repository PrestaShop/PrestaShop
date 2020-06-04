require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddWebserviceKey extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Webservice •';
    this.pageTitleEdit = 'Webservice •';

    // Selectors
    this.generateButton = 'button.js-generator-btn';
    this.keyDescriptionTextarea = '#webservice_key_description';
    this.statusSwitchLabel = 'label[for="webservice_key_status_%ID"]';
    this.saveButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit webservice key
   * @param webserviceData
   * @return {Promise<textContent>}
   */
  async createEditWebservice(webserviceData) {
    await this.page.click(this.generateButton);
    await this.setValue(this.keyDescriptionTextarea, webserviceData.keyDescription);
    // replace %ID by 1 in the selector if active = YES / 0 if active = NO
    await this.page.click(this.statusSwitchLabel.replace('%ID', webserviceData.status ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
