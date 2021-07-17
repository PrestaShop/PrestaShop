require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddWebserviceKey extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @param webserviceData
   * @param toGenerate
   * @returns {Promise<string>}
   */
  async createEditWebservice(page, webserviceData, toGenerate = true) {
    if (toGenerate) {
      await page.click(this.generateButton);
    } else {
      await this.setValue(page, this.webserviceKeyInput, webserviceData.key);
    }

    await this.setValue(page, this.keyDescriptionTextarea, webserviceData.keyDescription);
    // id = 1 if active = YES / 0 if active = NO
    await page.click(this.statusSwitchLabel(webserviceData.status ? 1 : 0));

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddWebserviceKey();
