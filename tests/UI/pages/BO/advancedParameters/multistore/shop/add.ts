import BOBasePage from '@pages/BO/BObasePage';

import type ShopData from '@data/faker/shop';

import type {Page} from 'playwright';

/**
 * Add shop page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddShop extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly storeForm: string;

  private readonly nameInput: string;

  private readonly colorInput: string;

  private readonly shopGroupSelect: string;

  private readonly categoryRootSelect: string;

  private readonly saveButton: string;

  private readonly sourceStoreSelect: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add shop page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.storeForm = '#shop_form';
    this.nameInput = '#name';
    this.colorInput = '#color_0';
    this.shopGroupSelect = '#id_shop_group';
    this.categoryRootSelect = '#id_category';
    this.saveButton = `${this.storeForm} #fieldset_0 #shop_form_submit_btn`;
    this.sourceStoreSelect = '#importFromShop';
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
  async setShop(page: Page, shopData: ShopData): Promise<string> {
    const currentUrl: string = page.url();

    await this.setValue(page, this.nameInput, shopData.name);
    await this.selectByVisibleText(page, this.shopGroupSelect, shopData.shopGroup);
    await this.setValue(page, this.colorInput, shopData.color);
    await this.selectByVisibleText(page, this.categoryRootSelect, shopData.categoryRoot);

    await Promise.all([
      page.locator(this.saveButton).evaluate((el: HTMLElement) => el.click()),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle', timeout: 30000}),
    ]);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get source store
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSourceStore(page: Page): Promise<string> {
    return this.getTextContent(page, `${this.sourceStoreSelect} option[selected]`, false);
  }
}

export default new AddShop();
