require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddPageCategory extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Pages';

    // Selectors
    this.nameInput = '#cms_page_category_name_1';
    this.displayed = id => `label[for='cms_page_category_is_displayed_${id}']`;
    this.descriptionIframe = '#cms_page_category_description_1';
    this.metaTitleInput = '#cms_page_category_meta_title_1';
    this.metaDescriptionInput = '#cms_page_category_meta_description_1';
    this.metaKeywordsInput = '#cms_page_category_meta_keywords_1-tokenfield';
    this.saveCategoryButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page category
   * @param page
   * @param pageCategoryData
   * @returns {Promise<string>}
   */
  async createEditPageCategory(page, pageCategoryData) {
    await this.setValue(page, this.nameInput, pageCategoryData.name);
    await page.click(this.displayed(pageCategoryData.displayed ? 1 : 0));
    await this.setValue(page, this.descriptionIframe, pageCategoryData.description);
    await this.setValue(page, this.metaTitleInput, pageCategoryData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageCategoryData.metaDescription);
    await this.setValue(page, this.metaKeywordsInput, pageCategoryData.metaKeywords);
    await this.clickAndWaitForNavigation(page, this.saveCategoryButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}
module.exports = new AddPageCategory();
