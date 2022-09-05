require('module-alias/register');
const themeAndLogoBasePage = require('@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage');

/**
 * Advanced customization page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class AdvancedCustomization extends themeAndLogoBasePage {
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
   * @returns {Promise<string>}
   */
  async downloadTheme(page) {
    return this.clickAndWaitForDownload(page, this.downloadThemeButton);
  }

  /**
   * Click on upload a child theme
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnUploadChildThemeButton(page) {
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
  async uploadTheme(page, filePath) {
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialogUploadChildThemeSelectLink),
      this.uploadOnFileChooser(page, this.modalDialogUploadChildThemeSelectLink, filePath),
    ]);

    return this.getTextContent(page, this.successMsgUploadChildTheme);
  }

  /**
   * Close the modal of the Upload child theme
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeModal(page) {
    await this.waitForSelectorAndClick(page, this.modalCloseButton);
    return this.elementNotVisible(page, this.modalDialogUploadChildTheme, 3000);
  }

  /**
   * Get the link How to use Parent's child theme
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getHowToUseParentsChildThemesLink(page) {
    return this.getAttributeContent(page,
      this.howToUseParentsChildThemesLink,
      'href',
    );
  }
}

module.exports = new AdvancedCustomization();
