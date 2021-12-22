require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add profile page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddProfile extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add profile page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new profile';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#profile_name_1';
    this.saveButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page profile
   * @param page {Page} Browser tab
   * @param profileData {ProfileData} Data to set on add/edit profile form
   * @return {Promise<string>}
   */
  async createEditProfile(page, profileData) {
    await this.setValue(page, this.nameInput, profileData.name);
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddProfile();
