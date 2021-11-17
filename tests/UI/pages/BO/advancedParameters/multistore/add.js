require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add shop page page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddShopGroup extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add shop page page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Multistore > Add new •';
    this.pageTitleEdit = 'Multistore > Edit:';

    // Selectors
    this.shopGroupForm = '#shop_group_form';
    this.nameInput = '#name';
    this.shareCustomersToggleInput = toggle => `${this.shopGroupForm} #share_customer_${toggle}`;
    this.shareAvailableQuantitiesToggleLabel = toggle => `${this.shopGroupForm} #share_customer_${toggle}`;
    this.statusToggleLabel = toggle => `${this.shopGroupForm} #share_customer_${toggle}`;
    this.saveButton = '#shop_group_form_submit_btn';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit shop group
   * @param page {Page} Browser tab
   * @param shopGroupData {ShopGroupData} Data to set on add/edit shop group form
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
