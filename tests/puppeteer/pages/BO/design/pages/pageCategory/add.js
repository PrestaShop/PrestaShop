require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPageCategory extends BOBasePage {
  constructor(page) {
    super(page);

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
   * @param pageCategoryData
   * @return {Promise<textContent>}
   */
  async createEditPageCategory(pageCategoryData) {
    await this.setValue(this.nameInput, pageCategoryData.name);
    await this.page.click(this.displayed(pageCategoryData.displayed ? 1 : 0));
    await this.setValue(this.descriptionIframe, pageCategoryData.description);
    await this.setValue(this.metaTitleInput, pageCategoryData.metaTitle);
    await this.setValue(this.metaDescriptionInput, pageCategoryData.metaDescription);
    await this.setValue(this.metaKeywordsInput, pageCategoryData.metaKeywords);
    await this.clickAndWaitForNavigation(this.saveCategoryButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
