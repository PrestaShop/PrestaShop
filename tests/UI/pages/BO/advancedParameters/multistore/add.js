require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddShopGroup extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Multistore > Add new •';
    this.pageTitleEdit = 'Multistore > Edit:';

    // Selectors
    this.shopGroupForm = '#shop_group_form';
    this.nameInput = '#name';
    this.shareCustomersToggleInput = toggle => `${this.shopGroupForm} label[for='share_customer_${toggle}']`;
    this.shareAvailableQuantitiesToggleLabel = toggle => `${this.shopGroupForm} label[for='share_customer_${toggle}']`;
    this.statusToggleLabel = toggle => `${this.shopGroupForm} label[for='share_customer_${toggle}']`;
    this.saveButton = '#shop_group_form_submit_btn';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit shop group
   * @param page
   * @param shopGroupData
   * @returns {Promise<string>}
   */
  async setShopGroup(page, shopGroupData) {
    await this.setValue(page, this.nameInput, shopGroupData.name);

    await page.check(this.shareCustomersToggleInput(shopGroupData.shareCustomer ? 'on' : 'off'));
    await page.check(this.shareAvailableQuantitiesToggleLabel(shopGroupData.shareAvailableQuantities ? 'on' : 'off'));
    await page.check(this.statusToggleLabel(shopGroupData.status ? 'on' : 'off'));

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddShopGroup();
