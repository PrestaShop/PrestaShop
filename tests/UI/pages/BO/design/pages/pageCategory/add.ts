import BOBasePage from '@pages/BO/BObasePage';

import type CMSCategoryData from '@data/faker/CMScategory';

import type {Page} from 'playwright';

/**
 * Add page category page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddPageCategory extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly displayedToggleInput: (toggle: number) => string;

  private readonly descriptionIframe: string;

  private readonly metaTitleInput: string;

  private readonly metaDescriptionInput: string;

  private readonly saveCategoryButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add page category page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New category • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing category';

    // Selectors
    this.nameInput = '#cms_page_category_name_1';
    this.displayedToggleInput = (toggle: number) => `#cms_page_category_is_displayed_${toggle}`;
    this.descriptionIframe = '#cms_page_category_description_1';
    this.metaTitleInput = '#cms_page_category_meta_title_1';
    this.metaDescriptionInput = '#cms_page_category_meta_description_1';
    this.saveCategoryButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page category
   * @param page {Page} Browser tab
   * @param pageCategoryData {CMSCategoryData} Data to set on page category form
   * @returns {Promise<string>}
   */
  async createEditPageCategory(page: Page, pageCategoryData: CMSCategoryData): Promise<string> {
    await this.setValue(page, this.nameInput, pageCategoryData.name);
    await this.setChecked(page, this.displayedToggleInput(pageCategoryData.displayed ? 1 : 0));
    await this.setValue(page, this.descriptionIframe, pageCategoryData.description);
    await this.setValue(page, this.metaTitleInput, pageCategoryData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageCategoryData.metaDescription);
    await this.clickAndWaitForURL(page, this.saveCategoryButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddPageCategory();
