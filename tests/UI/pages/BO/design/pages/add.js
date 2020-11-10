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
    this.indexationToggleInput = toggle => `#cms_page_is_indexed_for_search_${toggle}`;
    this.displayedToggleInput = toggle => `#cms_page_is_displayed_${toggle}`;
    this.savePageButton = '#save-button';
    this.saveAndPreviewPageButton = '#save-and-preview-button';
    this.cancelButton = '#cancel-link';
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
    // Fill form
    await this.setValue(page, this.titleInput, pageData.title);
    await this.setValue(page, this.metaTitleInput, pageData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageData.metaDescription);
    await this.setValue(page, this.metaKeywordsInput, pageData.metaKeywords);
    await this.setValueOnTinymceInput(page, this.pageContentIframe, pageData.content);
    await page.check(this.indexationToggleInput(pageData.indexation ? 1 : 0));
    await page.check(this.displayedToggleInput(pageData.displayed ? 1 : 0));

    // Save form
    await this.clickAndWaitForNavigation(page, this.savePageButton);

    // Return successful message
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
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
