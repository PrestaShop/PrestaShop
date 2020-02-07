require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class moduleManager extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Module manager •';

    // Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';
    this.allModulesBloc = '.module-short-list .module-item-list';
    this.moduleBloc = `${this.allModulesBloc}[data-name='%MODULENAME']`;
    this.disableModuleButton = `${this.moduleBloc} button.module_action_menu_disable`;
    this.configureModuleButton = `${this.moduleBloc} div.module-actions>a`;
    // Status dropdown selectors
    this.statusDropdownDiv = '#module-status-dropdown';
    this.statusDropdownMenu = 'div.ps-dropdown-menu[aria-labelledby=\'module-status-dropdown\']';
    this.statusDropdownItemLink = `${this.statusDropdownMenu} ul li[data-status-ref='%REF'] a`;
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

  /**
   * Filter modules by status
   * @param enabled
   * @return {Promise<void>}
   */
  async filterByStatus(enabled) {
    await Promise.all([
      this.page.click(this.statusDropdownDiv),
      this.page.waitForSelector(`${this.statusDropdownDiv}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.statusDropdownItemLink.replace('%REF', enabled ? 1 : 0)),
      this.page.waitForSelector(`${this.statusDropdownDiv}[aria-expanded='false']`),
    ]);
  }

  /**
   * Get status of module (enable/disable)
   * @param moduleName
   * @return {Promise<boolean|true>}
   */
  async isModuleEnabled(moduleName) {
    return this.elementNotVisible(this.disableModuleButton.replace('%MODULENAME', moduleName), 1000);
  }

  /**
   * Get all modules status
   * @return {Promise<void>}
   */
  async getAllModulesStatus() {
    const modulesStatus = [];
    const allModulesNames = await this.getAllModulesNames();
    for (let i = 0; i < allModulesNames.length; i++) {
      const moduleStatus = await this.isModuleEnabled();
      await modulesStatus.push({name: allModulesNames[i], status: moduleStatus});
    }
    return modulesStatus;
  }

  async getAllModulesNames() {
    return this.page.$$eval(
      this.allModulesBloc,
      all => all.map(el => el.getAttribute('data-name')),
    );
  }
};
