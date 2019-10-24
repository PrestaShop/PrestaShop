require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPageCategory extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Pages';

    // Selectors
    this.titleInput = '#cms_page_title_1';
    this.metaTitleInput = '#cms_page_meta_title_1';
    this.metaDescriptionInput = '#cms_page_meta_description_1';
    this.metaKeywordsInput = '#cms_page_meta_keyword_1-tokenfield';
    this.pageContentIframe = '#cms_page_content_1_ifr';
    this.indexation = '#cms_page_is_indexed_for_search_%ID';
    this.displayed = 'label[for="cms_page_is_displayed_%ID"]';
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
    if (pageData.indexation) await this.page.click(this.indexation.replace('%ID', '1'));
    else await this.page.click(this.indexation.replace('%ID', '0'));
    if (pageData.displayed) await this.page.click(this.displayed.replace('%ID', '1'));
    else await this.page.click(this.displayed.replace('%ID', '0'));
    await Promise.all([
      this.page.click(this.savePageButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  async previewPage() {
    this.page = await this.openLinkWithTargetBlank(this.page, this.saveAndPreviewPageButton, false);
    return this.page;
  }
};
