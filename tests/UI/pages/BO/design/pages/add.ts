import BOBasePage from '@pages/BO/BObasePage';

import type CMSPageData from '@data/faker/CMSpage';

import type {Page} from 'playwright';

/**
 * Add page page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddPage extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly editPageTitle: (pageTitle: string) => string;

  private readonly titleInput: string;

  private readonly metaTitleInput: string;

  private readonly metaDescriptionInput: string;

  private readonly metaKeywordsInput: string;

  private readonly pageContentIframe: string;

  private readonly indexationToggleInput: (toggle: number) => string;

  private readonly displayedToggleInput: (toggle: number) => string;

  private readonly savePageButton: string;

  private readonly saveAndPreviewPageButton: string;

  private readonly cancelButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add page page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New page • ${global.INSTALL.SHOP_NAME}`;
    this.editPageTitle = (pageTitle: string) => `Editing page ${pageTitle} • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.titleInput = '#cms_page_title_1';
    this.metaTitleInput = '#cms_page_meta_title_1';
    this.metaDescriptionInput = '#cms_page_meta_description_1';
    this.metaKeywordsInput = '#cms_page_meta_keyword_1-tokenfield';
    this.pageContentIframe = '#cms_page_content_1_ifr';
    this.indexationToggleInput = (toggle: number) => `#cms_page_is_indexed_for_search_${toggle}`;
    this.displayedToggleInput = (toggle: number) => `#cms_page_is_displayed_${toggle}`;
    this.savePageButton = '#save-button';
    this.saveAndPreviewPageButton = '#save-and-preview-button';
    this.cancelButton = '#cancel-link';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page category
   * @param page {Page} Browser tab
   * @param pageData {CMSPageData} Data to set on new/edit page form
   * @return {Promise<string>}
   */
  async createEditPage(page: Page, pageData: CMSPageData): Promise<string> {
    // Fill form
    await this.setValue(page, this.titleInput, pageData.title);
    await this.setValue(page, this.metaTitleInput, pageData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput, pageData.metaDescription);
    await this.setValue(page, this.metaKeywordsInput, pageData.metaKeywords);
    await this.setValueOnTinymceInput(page, this.pageContentIframe, pageData.content);
    await this.setChecked(page, this.indexationToggleInput(pageData.indexation ? 1 : 0));
    await this.setChecked(page, this.displayedToggleInput(pageData.displayed ? 1 : 0));

    // Save form
    await this.clickAndWaitForURL(page, this.savePageButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Preview page in new tab
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async previewPage(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.saveAndPreviewPageButton);
  }

  /**
   * Cancel page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async cancelPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.cancelButton);
  }
}

export default new AddPage();
