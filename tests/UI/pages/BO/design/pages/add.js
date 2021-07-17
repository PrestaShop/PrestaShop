require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddPage extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Pages';

    // Selectors
    this.titleInput = '#cms_page_title_1';
    this.metaTitleInput = '#cms_page_meta_title_1';
    this.metaDescriptionInput = '#cms_page_meta_description_1';
    this.metaKeywordsInput = '#cms_page_meta_keyword_1-tokenfield';
    this.pageContentIframe = '#cms_page_content_1_ifr';
    this.indexation = id => `#cms_page_is_indexed_for_search_${id}`;
    this.displayed = id => `label[for='cms_page_is_displayed_${id}']`;
    this.savePageButton = 'div.card-footer button.ml-3';
    this.saveAndPreviewPageButton = 'div.card-footer button';
    this.cancelButton = 'div.card-footer .btn-outline-secondary';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page category
   * @param page
   * @param pageData
   * @return {Promise<void>}
   */
  async createEditPage(page, pageData) {
    await this.setValue(page, this.titleInput, pageData.title);
    await this.setValue(page, this.metaTitleInput, pageData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageData.metaDescription);
    await this.setValue(page, this.metaKeywordsInput, pageData.metaKeywords);
    await this.setValueOnTinymceInput(page, this.pageContentIframe, pageData.content);
    await page.click(this.indexation(pageData.indexation ? 1 : 0));
    await page.click(this.displayed(pageData.displayed ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.savePageButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Preview page in new tab
   * @param page
   * @return page opened
   */
  async previewPage(page) {
    return this.openLinkWithTargetBlank(page, this.saveAndPreviewPageButton);
  }

  /**
   * Cancel page
   * @param page
   * @return {Promise<void>}
   */
  async cancelPage(page) {
    await this.clickAndWaitForNavigation(page, this.cancelButton);
  }
}
module.exports = new AddPage();
