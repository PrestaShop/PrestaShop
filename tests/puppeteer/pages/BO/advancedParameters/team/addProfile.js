require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddProfile extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Add new profile • prestashop';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#profile_name_1';
    this.saveButton = 'div.card-footer button';
    this.cancelButton = 'div.card-footer a';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page profile
   * @param profileData
   * @return {Promise<textContent>}
   */
  async createEditProfile(profileData) {
    await this.setValue(this.nameInput, profileData.name);
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
