const BOCommonPage = require('./BO_commonPage');

module.exports = class BO_MODULE_MANAGER extends BOCommonPage {
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

  async searchModule(moduleTag, moduleName) {
    await this.page.type(this.searchModuleTagInput, moduleTag);
    await this.page.click(this.searchModuleButton);
    await this.page.waitForSelector(this.moduleBloc.replace('%MODULENAME', moduleName), {visible: true});
  }

  async goToConfigurationPage(moduleName) {
    await this.page.click(this.configureModuleButton.replace('%MODULENAME', moduleName));
  }
};
