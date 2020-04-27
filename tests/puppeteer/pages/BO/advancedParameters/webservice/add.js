require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddWebserviceKey extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Webservice •';
    this.pageTitleEdit = 'Webservice •';

    // Selectors
    this.webserviceKeyInput = '#webservice_key_key';
    this.generateButton = 'button.js-generator-btn';
    this.keyDescriptionTextarea = '#webservice_key_description';
    this.statusSwitchLabel = id => `label[for='webservice_key_status_${id}']`;
    this.saveButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit webservice key
   * @param webserviceData
   * @param toGenerate
   * @return {Promise<textContent>}
   */
  async createEditWebservice(webserviceData, toGenerate = true) {
    if (toGenerate) await this.page.click(this.generateButton);
    else await this.setValue(this.webserviceKeyInput, webserviceData.key);
    await this.setValue(this.keyDescriptionTextarea, webserviceData.keyDescription);
    // id = 1 if active = YES / 0 if active = NO
    await this.page.click(this.statusSwitchLabel(webserviceData.status ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
