require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class moduleManager extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Module manager •';

    // Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';
    this.moduleBloc = '.module-short-list .module-item-list[data-name=\'%MODULENAME\']';
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
   * Click on button configure of a module
   * @param moduleName, Name of module
   * @return {Promise<void>}
   */
  async goToConfigurationPage(moduleName) {
    await this.page.click(this.configureModuleButton.replace('%MODULENAME', moduleName));
  }
};
