require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class moduleCatalog extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Module Catalog •';
    this.installMessageSuccessful = 'Install action on module %MODULETAG succeeded.';

    // Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';
    this.moduleBloc = '#modules-list-container-all div[data-name=\'%MODULENAME\']:not([style])';
    this.installModuleButton = `${this.moduleBloc} form>button.module_action_menu_install`;
    this.configureModuleButton = `${this.moduleBloc} div.module-actions>a`;
  }

  /*
  Methods
   */

  /**
   * Search Module in Page module Catalog
   * @param moduleTag, Tag of Module
   * @param moduleName, Name of module
   * @return {Promise<void>}
   */
  async searchModule(moduleTag, moduleName) {
    await this.page.type(this.searchModuleTagInput, moduleTag);
    await this.page.click(this.searchModuleButton);
    await this.page.waitForSelector(this.moduleBloc.replace('%MODULENAME', moduleName), {visible: true});
  }

  /**
   * Install Module and waiting for Successful massage
   * @param moduleName, Name of module
   * @return {Promise<textContent>}
   */
  async installModule(moduleName) {
    await this.page.click(this.installModuleButton.replace('%MODULENAME', moduleName));
    await this.page.waitForSelector(this.growlMessageBlock, {visible: true});
    return this.getTextContent(this.growlMessageBlock);
  }
};
