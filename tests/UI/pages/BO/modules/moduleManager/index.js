require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Module manager page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ModuleManager extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on module manager page
   */
  constructor() {
    super();

    this.pageTitle = 'Module manager •';

    // Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';
    this.modulesListBlock = '.module-short-list:not([style=\'display: none;\'])';
    this.modulesListBlockTitle = `${this.modulesListBlock} span.module-search-result-title`;
    this.allModulesBlock = `${this.modulesListBlock} .module-item-list`;
    this.moduleBlock = moduleName => `${this.allModulesBlock}[data-name='${moduleName}']`;
    this.disableModuleButton = moduleName => `${this.moduleBlock(moduleName)} button.module_action_menu_disable`;
    this.enableModuleButton = moduleName => `${this.moduleBlock(moduleName)} button.module_action_menu_enable`;
    this.configureModuleButton = moduleName => `${this.moduleBlock(moduleName)}`
      + ' div.module-actions a[href*=\'/action/configure\']';
    this.actionsDropdownButton = moduleName => `${this.moduleBlock(moduleName)} button.dropdown-toggle`;
    // Status dropdown selectors
    this.statusDropdownDiv = '#module-status-dropdown';
    this.statusDropdownMenu = 'div.ps-dropdown-menu[aria-labelledby=\'module-status-dropdown\']';
    this.statusDropdownItemLink = ref => `${this.statusDropdownMenu} a[data-status-ref='${ref}']`;
    // Categories
    this.categoriesSelectDiv = '#categories';
    this.categoriesDropdownDiv = 'div.ps-dropdown-menu.dropdown-menu.module-category-selector';
    this.categoryDropdownItem = cat => `${this.categoriesDropdownDiv} a[data-category-display-name='${cat}']`;
  }

  /*
  Methods
   */

  /**
   * Search Module in Page module Catalog
   * @param page {Page} Browser tab
   * @param moduleTag {string} Tag of the Module
   * @param moduleName {string} Name of the module
   * @return {Promise<void>}
   */
  async searchModule(page, moduleTag, moduleName) {
    await page.type(this.searchModuleTagInput, moduleTag);
    await page.click(this.searchModuleButton);
    return this.elementVisible(page, this.moduleBlock(moduleName), 10000);
  }

  /**
   * Click on button configure of a module
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @return {Promise<void>}
   */
  async goToConfigurationPage(page, moduleName) {
    if (await this.elementNotVisible(page, this.configureModuleButton(moduleName), 1000)) {
      await Promise.all([
        page.click(this.actionsDropdownButton(moduleName)),
        this.waitForVisibleSelector(page, `${this.actionsDropdownButton(moduleName)}[aria-expanded='true']`),
      ]);
    }
    await page.click(this.configureModuleButton(moduleName));
  }

  /**
   * Filter modules by status
   * @param page {Page} Browser tab
   * @param status {boolean} Status to filter with
   * @return {Promise<void>}
   */
  async filterByStatus(page, status) {
    // Open dropdown
    await page.click(this.statusDropdownDiv);
    await this.waitForVisibleSelector(page, `${this.statusDropdownDiv}[aria-expanded='true']`);

    // Select dropdown item
    await page.click(this.statusDropdownItemLink(status ? 1 : 0));
    await this.waitForVisibleSelector(page, `${this.statusDropdownDiv}[aria-expanded='false']`);
  }

  /**
   * Get status of module (enable/disable)
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @return {Promise<boolean>}
   */
  async isModuleEnabled(page, moduleName) {
    return this.elementNotVisible(page, this.enableModuleButton(moduleName), 1000);
  }

  /**
   * Get all modules status
   * @param page {Page} Browser tab
   * @returns {Promise<Array<{name: string, status: boolean}>>}
   */
  async getAllModulesStatus(page) {
    const modulesStatus = [];
    const allModulesNames = await this.getAllModulesNames(page);

    for (let i = 0; i < allModulesNames.length; i++) {
      const moduleStatus = await this.isModuleEnabled(page, allModulesNames[i]);
      modulesStatus.push({name: allModulesNames[i], status: moduleStatus});
    }
    return modulesStatus;
  }

  /**
   * Get All modules names
   * @param page {Page} Browser tab
   * @return {Promise<Array<string>>}
   */
  async getAllModulesNames(page) {
    return page.$$eval(
      this.allModulesBlock,
      all => all.map(el => el.getAttribute('data-name')),
    );
  }

  /**
   * Filter by category
   * @param page {Page} Browser tab
   * @param category {string} Name of module's category to filter with
   * @return {Promise<void>}
   */
  async filterByCategory(page, category) {
    await Promise.all([
      page.click(this.categoriesSelectDiv),
      this.waitForVisibleSelector(page, `${this.categoriesSelectDiv}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.categoryDropdownItem(category)),
      this.waitForVisibleSelector(page, `${this.categoriesSelectDiv}[aria-expanded='false']`),
    ]);
  }

  /**
   * Get modules block title (administration / payment ...)
   * @param page {Page} Browser tab
   * @param position {number} Position of the module on the list
   * @return {Promise<void>}
   */
  async getBlockModuleTitle(page, position) {
    const modulesBlocks = await page.$$eval(this.modulesListBlockTitle, all => all.map(el => el.textContent));

    return modulesBlocks[position - 1];
  }
}

module.exports = new ModuleManager();
