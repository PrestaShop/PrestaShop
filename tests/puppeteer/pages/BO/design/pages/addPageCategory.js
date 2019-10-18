require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPageCategory extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Pages';

    // Selectors
    this.nameInput = '#cms_page_category_name_1';
    this.displayed = 'label[for=\'cms_page_category_is_displayed_%ID\']';
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
    if (pageCategoryData.displayed) await this.page.click(this.displayed.replace('%ID', '1'));
    else await this.page.click(this.displayed.replace('%ID', '0'));
    await this.setValue(this.descriptionIframe, pageCategoryData.description);
    await this.setValue(this.metaTitleInput, pageCategoryData.metaTitle);
    await this.setValue(this.metaDescriptionInput, pageCategoryData.metaDescription);
    await this.setValue(this.metaKeywordsInput, pageCategoryData.metaKeywords);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
      this.page.click(this.saveCategoryButton),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
