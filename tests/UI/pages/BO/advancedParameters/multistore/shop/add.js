require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add shop page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddShop extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add shop page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#name';
    this.colorInput = '#color_0';
    this.shopGroupSelect = '#id_shop_group';
    this.categoryRootSelect = '#id_category';
    this.saveButton = '#shop_form_submit_btn';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit shop
   * @param page {Page} Browser tab
   * @param shopData {ShopData} Data to set on create/edit shop form
   * @returns {Promise<string>}
   */
  async setShop(page, shopData) {
    await this.setValue(page, this.nameInput, shopData.name);
    await this.selectByVisibleText(page, this.shopGroupSelect, shopData.shopGroup);
    await this.selectByVisibleText(page, this.categoryRootSelect, shopData.categoryRoot);

    await this.clickAndWaitForNavigation(page, this.saveButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddShop();
