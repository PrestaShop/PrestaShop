require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Module catalog page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ModuleCatalog extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on module catalog page
   */
  constructor() {
    super();

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
   * @param page {Page} Browser tab
   * @param moduleTag {string} Tag of the module
   * @param moduleName {string} Name of the module
   * @return {Promise<void>}
   */
  async searchModule(page, moduleTag, moduleName) {
    await page.type(this.searchModuleTagInput, moduleTag);
    await page.click(this.searchModuleButton);
    await this.waitForVisibleSelector(page, this.moduleBloc(moduleName));
  }

  /**
   * Install Module and waiting for Successful massage
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @returns {Promise<string>}
   */
  async installModule(page, moduleName) {
    await page.click(this.installModuleButton(moduleName));
    return this.getGrowlMessageContent(page);
  }
}

module.exports = new ModuleCatalog();
