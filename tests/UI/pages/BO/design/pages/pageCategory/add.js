require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add page category page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddPageCategory extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add page category page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Pages';

    // Selectors
    this.nameInput = '#cms_page_category_name_1';
    this.displayedToggleInput = toggle => `#cms_page_category_is_displayed_${toggle}`;
    this.descriptionIframe = '#cms_page_category_description_1';
    this.metaTitleInput = '#cms_page_category_meta_title_1';
    this.metaDescriptionInput = '#cms_page_category_meta_description_1';
    this.metaKeywordsInput = '#cms_page_category_meta_keywords_1-tokenfield';
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
  async createEditPageCategory(page, pageCategoryData) {
    await this.setValue(page, this.nameInput, pageCategoryData.name);
    await page.check(this.displayedToggleInput(pageCategoryData.displayed ? 1 : 0));
    await this.setValue(page, this.descriptionIframe, pageCategoryData.description);
    await this.setValue(page, this.metaTitleInput, pageCategoryData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageCategoryData.metaDescription);
    await this.setValue(page, this.metaKeywordsInput, pageCategoryData.metaKeywords);
    await this.clickAndWaitForNavigation(page, this.saveCategoryButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddPageCategory();
