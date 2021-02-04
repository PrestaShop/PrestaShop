require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddShop extends BOBasePage {
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
   * @param page
   * @param shopData
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
