require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddProfile extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Add new profile';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#profile_name_1';
    this.saveButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page profile
   * @param page
   * @param profileData
   * @return {Promise<string>}
   */
  async createEditProfile(page, profileData) {
    await this.setValue(page, this.nameInput, profileData.name);
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddProfile();
