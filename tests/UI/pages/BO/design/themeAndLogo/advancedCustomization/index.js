require('module-alias/register');
const themeAndLogoBasePage = require('@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage');

/**
 * Advanced customization page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
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
    this.uploadChildThemeButton = `#psthemecusto a[data-target= '${this.uploadChildThemeModal}']`;
    this.modalDialogUploadChildTheme = `${this.uploadChildThemeModal} div[role='document']`;
    this.childThemeImportDropZone = '#importDropzone';
    this.modalDialogUploadChildThemeSelectLink = `${this.childThemeImportDropZone} a.module-import-start-select-manual`;
    this.successMsgUploadChildTheme = `${this.childThemeImportDropZone} .module-import-success`;
  }

  /* Methods */
  /**
   * Download theme
   * @param page
   * @returns {Promise<string>}
   */
  async downloadTheme(page) {
    return this.clickAndWaitForDownload(page, this.downloadThemeButton);
  }

  /**
   * Click on upload a child theme
   * @param page
   * @returns {Promise<string>}
   */
  async clickOnUploadChildThemeButton(page) {
    await this.waitForSelectorAndClick(page, this.uploadChildThemeButton);
    await this.waitForVisibleSelector(page, this.modalDialogUploadChildTheme);

    return this.getTextContent(page, this.modalDialogUploadChildTheme);
  }

  /**
   * Upload child theme
   * @param page
   * @param filePath
   * @returns {Promise<string>}
   */
  async uploadTheme(page, filePath) {
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialogUploadChildThemeSelectLink),
      this.uploadOnFileChooser(page, this.modalDialogUploadChildThemeSelectLink, filePath),
    ]);

    return this.getTextContent(page, this.successMsgUploadChildTheme);
  }
}

module.exports = new AdvancedCustomization();
