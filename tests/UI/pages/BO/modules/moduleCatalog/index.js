require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class moduleCatalog extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Module Catalog •';
    this.installMessageSuccessful = moduleTag => `Install action on module ${moduleTag} succeeded.`;

    // Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';
    this.moduleBloc = moduleName => `#modules-list-container-all div[data-name='${moduleName}']:not([style])`;
    this.installModuleButton = moduleName => `${this.moduleBloc(moduleName)} form>button.module_action_menu_install`;
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
    await this.waitForVisibleSelector(this.moduleBloc(moduleName));
  }

  /**
   * Install Module and waiting for Successful massage
   * @param moduleName, Name of module
   * @returns {Promise<string>}
   */
  async installModule(moduleName) {
    await this.page.click(this.installModuleButton(moduleName));
    return this.getTextContent(this.growlMessageBlock);
  }
};
