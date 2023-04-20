import BOBasePage from '@pages/BO/BObasePage';

import ModuleData from '@data/faker/module';

import type {Page} from 'playwright';

/**
 * Module manager page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ModuleManager extends BOBasePage {
  public readonly pageTitle: string;

  private readonly searchModuleTagInput: string;

  private readonly searchModuleButton: string;

  private readonly modulesListBlock: string;

  private readonly modulesListBlockTitle: string;

  private readonly allModulesBlock: string;

  private readonly moduleBlocks: string;

  private readonly moduleBlock: (moduleName: string) => string;

  private readonly disableModuleButton: (moduleName: string) => string;

  private readonly enableModuleButton: (moduleName: string) => string;

  private readonly allModulesInstallModuleButton: (moduleName: string) => string;

  private readonly configureModuleButton: (moduleName: string) => string;

  private readonly actionsDropdownButton: (moduleName: string) => string;

  private readonly installModuleButton: (moduleTag: string) => string;

  private readonly uninstallModuleModalConfirmButton: (moduleTag: string) => string;

  private readonly modalConfirmAction: (moduleTag: string) => string;

  private readonly uninstallModuleButton: string;

  private readonly statusDropdownDiv: string;

  private readonly statusDropdownMenu: string;

  private readonly statusDropdownItemLink: (ref: number) => string;

  private readonly filterByAllModulesButton: string;

  private readonly categoriesSelectDiv: string;

  private readonly categoriesDropdownDiv: string;

  private readonly categoryDropdownItem: (cat: string) => string;

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
    this.moduleBlocks = 'div.module-short-list';
    this.moduleBlock = (moduleName: string) => `${this.allModulesBlock}[data-name='${moduleName}']`;
    this.disableModuleButton = (moduleName: string) => `${this.moduleBlock(moduleName)} button.module_action_menu_disable`;
    this.enableModuleButton = (moduleName: string) => `${this.moduleBlock(moduleName)} button.module_action_menu_enable`;
    this.allModulesInstallModuleButton = (moduleName: string) => `${this.moduleBlock(moduleName)}`
      + ' button.module_action_menu_install';
    this.actionsDropdownButton = (moduleName: string) => `${this.moduleBlock(moduleName)} button.dropdown-toggle`;
    this.installModuleButton = (moduleTag: string) => `div[data-tech-name='${moduleTag}'] button.module_action_menu_install`;
    this.configureModuleButton = (moduleName: string) => `${this.moduleBlock(moduleName)}`
      + ' div.module-actions a[href*=\'/action/configure\']';
    this.uninstallModuleButton = 'div.btn-group.module-actions.show button.module_action_menu_uninstall';
    this.modalConfirmAction = (moduleTag: string) => `#module-modal-confirm-${moduleTag}`;
    this.uninstallModuleModalConfirmButton = (moduleTag: string) => `${this.modalConfirmAction(moduleTag)}-uninstall`
      + ' div.modal-footer a.module_action_modal_uninstall';
    // Status dropdown selectors
    this.statusDropdownDiv = '#module-status-dropdown';
    this.statusDropdownMenu = 'div.ps-dropdown-menu[aria-labelledby=\'module-status-dropdown\']';
    this.statusDropdownItemLink = (ref: number) => `${this.statusDropdownMenu} a[data-status-ref='${ref}']`;
    this.filterByAllModulesButton = '.module-status-reset';
    // Categories
    this.categoriesSelectDiv = '#categories';
    this.categoriesDropdownDiv = 'div.ps-dropdown-menu.dropdown-menu.module-category-selector';
    this.categoryDropdownItem = (cat: string) => `${this.categoriesDropdownDiv} a[data-category-display-name='${cat}']`;
  }

  /*
  Methods
   */

  /**
   * Search Module in Page module Catalog
   * @param page {Page} Browser tab
   * @param module {ModuleData} Tag of the Module
   * @return {Promise<boolean>}
   */
  async searchModule(page: Page, module: ModuleData): Promise<boolean> {
    await page.type(this.searchModuleTagInput, module.tag);
    await page.click(this.searchModuleButton);
    return this.elementVisible(page, this.moduleBlock(module.name), 10000);
  }

  /**
   * Click on button configure of a module
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @return {Promise<void>}
   */
  async goToConfigurationPage(page: Page, moduleName: string): Promise<void> {
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
   * @param status {string} Status to filter with
   * @return {Promise<void>}
   */
  async filterByStatus(page: Page, status: string): Promise<void> {
    // Open dropdown
    await page.click(this.statusDropdownDiv);
    await this.waitForVisibleSelector(page, `${this.statusDropdownDiv}[aria-expanded='true']`);

    // Select dropdown item
    let statusSelector: string;

    switch (status) {
      case 'all-Modules':
        statusSelector = this.filterByAllModulesButton;
        break;

      case 'enabled':
        statusSelector = this.statusDropdownItemLink(1);
        break;

      case 'disabled':
        statusSelector = this.statusDropdownItemLink(0);
        break;

      case 'uninstalled':
        statusSelector = this.statusDropdownItemLink(2);
        break;

      case 'installed':
        statusSelector = this.statusDropdownItemLink(3);
        break;

      default:
        throw new Error(`Status ${status} was not exist!`);
    }

    await page.click(statusSelector);
    await this.waitForVisibleSelector(page, `${this.statusDropdownDiv}[aria-expanded='false']`);
  }

  /**
   * Get number of blocks
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfBlocks(page: Page): Promise<number> {
    return (await page.$$(this.moduleBlocks)).length;
  }

  /**
   * Get status of module (enable/disable)
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @return {Promise<boolean>}
   */
  async isModuleEnabled(page: Page, moduleName: string): Promise<boolean> {
    return this.elementNotVisible(page, this.enableModuleButton(moduleName), 1000);
  }

  /**
   * Get status of module ( installed/uninstalled)
   * @param page {Page} Browser tab
   * @param moduleName {string} Name of the module
   * @return {Promise<boolean>}
   */
  async isModuleInstalled(page: Page, moduleName: string): Promise<boolean> {
    return this.elementNotVisible(page, this.allModulesInstallModuleButton(moduleName), 1000);
  }

  /**
   * Get all modules status
   * @param page {Page} Browser tab
   * @param statusToFilterBy {string} Status to filter by
   * @returns {Promise<Array<{ name: string, status: number }[]>>}
   */
  async getAllModulesStatus(page: Page, statusToFilterBy: string): Promise<{ name: string, status: boolean }[]> {
    const modulesStatus: { name: string, status: boolean }[] = [];
    const allModulesNames = await this.getAllModulesNames(page);

    for (let i = 0; i < allModulesNames.length; i++) {
      const moduleName: string | null = allModulesNames[i];

      if (typeof moduleName === 'string') {
        if (statusToFilterBy === 'enabled' || statusToFilterBy === 'disabled') {
          const moduleStatus = await this.isModuleEnabled(page, moduleName);
          modulesStatus.push({name: moduleName, status: moduleStatus});
        }

        if (statusToFilterBy === 'installed' || statusToFilterBy === 'uninstalled') {
          const moduleStatus = await this.isModuleInstalled(page, moduleName);
          modulesStatus.push({name: moduleName, status: moduleStatus});
        }
      }
    }

    return modulesStatus;
  }

  /**
   * Get All modules names
   * @param page {Page} Browser tab
   * @return {Promise<Array<string>>}
   */
  async getAllModulesNames(page: Page): Promise<(string | null)[]> {
    return page.$$eval(
      this.allModulesBlock,
      (all) => all.map((el) => el.getAttribute('data-name')),
    );
  }

  /**
   * Uninstall/install module
   * @param page {Page} Browser tab
   * @param module {ModuleData} Module data to install/uninstall
   * @param toInstall {boolean} True if we need to install module
   * @return {Promise<string | null>}
   */
  async installUninstallModule(page: Page, module: ModuleData, toInstall: boolean): Promise<string | null> {
    await this.closeGrowlMessage(page);

    if (toInstall) {
      await this.waitForSelectorAndClick(page, this.installModuleButton(module.tag));
    } else {
      await Promise.all([
        page.click(this.actionsDropdownButton(module.name)),
        this.waitForVisibleSelector(page, `${this.actionsDropdownButton(module.name)}[aria-expanded='true']`),
      ]);
      await this.waitForSelectorAndClick(page, this.uninstallModuleButton);
      await this.waitForSelectorAndClick(page, this.uninstallModuleModalConfirmButton(module.tag));
    }

    return this.getGrowlMessageContent(page);
  }

  /**
   * Filter by category
   * @param page {Page} Browser tab
   * @param category {string} Name of module's category to filter with
   * @return {Promise<void>}
   */
  async filterByCategory(page: Page, category: string): Promise<void> {
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
   * @return {Promise<string|null>}
   */
  async getBlockModuleTitle(page: Page, position: number): Promise<string | null> {
    const modulesBlocks = await page.$$eval(this.modulesListBlockTitle, (all) => all.map((el) => el.textContent));

    return modulesBlocks[position - 1];
  }
}

export default new ModuleManager();
