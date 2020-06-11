require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPage extends BOBasePage {
  constructor(page) {
    super(page);

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
   * @param pageData
   * @return {Promise<void>}
   */
  async createEditPage(pageData) {
    await this.setValue(this.titleInput, pageData.title);
    await this.setValue(this.metaTitleInput, pageData.metaTitle);
    await this.setValue(this.metaDescriptionInput, pageData.metaDescription);
    await this.setValue(this.metaKeywordsInput, pageData.metaKeywords);
    await this.setValueOnTinymceInput(this.pageContentIframe, pageData.content);
    await this.page.click(this.indexation(pageData.indexation ? 1 : 0));
    await this.page.click(this.displayed(pageData.displayed ? 1 : 0));
    await this.clickAndWaitForNavigation(this.savePageButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Preview page in new tab
   * @return page opened
   */
  async previewPage() {
    return this.openLinkWithTargetBlank(this.saveAndPreviewPageButton);
  }

  /**
   * Cancel page
   * @return {Promise<void>}
   */
  async cancelPage() {
    await this.clickAndWaitForNavigation(this.cancelButton);
  }
};
