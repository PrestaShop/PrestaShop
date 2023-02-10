import themeAndLogoBasePage from '@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage';
import {Page} from 'playwright';

/**
 * Advanced customization page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class AdvancedCustomization extends themeAndLogoBasePage {
  public readonly pageTitle: string;

  private readonly downloadThemeButton: string;

  private readonly uploadChildThemeModal: string;

  private readonly uploadChildThemeButton: string;

  private readonly modalDialogUploadChildTheme: string;

  private readonly modalCloseButton: string;

  private readonly childThemeImportDropZone: string;

  private readonly modalDialogUploadChildThemeSelectLink: string;

  private readonly successMsgUploadChildTheme: string;

  private readonly howToUseParentsChildThemesLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on advanced customization page
   */
  constructor() {
    super();

    this.pageTitle = 'Advanced Customization •';
    this.downloadThemeButton = '#download_child_theme';
    this.uploadChildThemeModal = '#upload-child-modal';
    this.uploadChildThemeButton = '#psthemecusto a[data-target=\'#upload-child-modal\']';
    this.modalDialogUploadChildTheme = `${this.uploadChildThemeModal} div[role='document']`;
    this.modalCloseButton = `${this.uploadChildThemeModal} .close`;
    this.childThemeImportDropZone = '#importDropzone';
    this.modalDialogUploadChildThemeSelectLink = `${this.childThemeImportDropZone} a.module-import-start-select-manual`;
    this.successMsgUploadChildTheme = `${this.childThemeImportDropZone} .module-import-success`;
    this.howToUseParentsChildThemesLink = '#psthemecusto .link-child';
  }

  /* Methods */
  /**
   * Download theme
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async downloadTheme(page: Page): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.downloadThemeButton);
  }

  /**
   * Click on upload a child theme
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnUploadChildThemeButton(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.uploadChildThemeButton);
    await this.waitForVisibleSelector(page, this.modalDialogUploadChildTheme);

    return this.getTextContent(page, this.modalDialogUploadChildTheme);
  }

  /**
   * Upload child theme
   * @param page {Page} Browser tab
   * @param filePath {string} Path of the file to add
   * @returns {Promise<string>}
   */
  async uploadTheme(page: Page, filePath: string): Promise<string> {
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialogUploadChildThemeSelectLink),
      this.uploadOnFileChooser(page, this.modalDialogUploadChildThemeSelectLink, [filePath]),
    ]);

    return this.getTextContent(page, this.successMsgUploadChildTheme);
  }

  /**
   * Close the modal of the Upload child theme
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeModal(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.modalCloseButton);
    return this.elementNotVisible(page, this.modalDialogUploadChildTheme, 3000);
  }

  /**
   * Get the link How to use Parent's child theme
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getHowToUseParentsChildThemesLink(page: Page): Promise<string> {
    return this.getAttributeContent(page,
      this.howToUseParentsChildThemesLink,
      'href',
    );
  }
}

export default new AdvancedCustomization();
