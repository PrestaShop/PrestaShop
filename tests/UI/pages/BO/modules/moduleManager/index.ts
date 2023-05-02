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

  public readonly disableModuleSuccessMessage: (moduleTag: string) => string;

  public readonly disableMobileSuccessMessage: (moduleTag: string) => string;

  public readonly enableModuleSuccessMessage: (moduleTag: string) => string;

  public readonly enableMobileSuccessMessage: (moduleTag: string) => string;

  public readonly resetModuleSuccessMessage: (moduleTag: string) => string;

  public readonly installModuleSuccessMessage: (moduleTag: string) => string;

  public readonly uninstallModuleSuccessMessage: (moduleTag: string) => string;

  private readonly searchModuleTagInput: string;

  private readonly searchModuleButton: string;

  private readonly topMenuDiv: string;

  private readonly bulkActionsButton: string;

  private readonly bulkActionsDropDownButton: string;

  private readonly bulkActionsDropDownList: string;

  private readonly bulkActionName: (action: string) => string;

  private readonly bulkActionsModal: string;

  private readonly bulkActionsModalConfirmButton: string;

  private readonly modulesListBlock: string;

  private readonly modulesListBlockTitle: string;

  private readonly allModulesBlock: string;

  private readonly moduleBlocks: string;

  private readonly moduleBlock: (moduleTag: string) => string;

  private readonly moduleCheckboxButton: (moduleTag: string) => string;

  private readonly actionModuleButton: (moduleTag: string, action: string) => string;

  private readonly configureModuleButton: (moduleTag: string) => string;

  private readonly actionsDropdownButton: (moduleTag: string) => string;

  private readonly actionModuleButtonInDropdownList: (action: string) => string;

  private readonly modalConfirmButton: (moduleTag: string, action: string) => string;

  private readonly modalConfirmAction: (moduleTag: string) => string;

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
    this.disableModuleSuccessMessage = (moduleTag: string) => `Disable action on module ${moduleTag} succeeded.`;
    this.disableMobileSuccessMessage = (moduleTag: string) => `Disable mobile action on module ${moduleTag} succeeded.`;
    this.enableModuleSuccessMessage = (moduleTag: string) => `Enable action on module ${moduleTag} succeeded.`;
    this.enableMobileSuccessMessage = (moduleTag: string) => `Enable mobile action on module ${moduleTag} succeeded.`;
    this.resetModuleSuccessMessage = (moduleTag: string) => `Reset action on module ${moduleTag} succeeded.`;
    this.installModuleSuccessMessage = (moduleTag: string) => `Install action on module ${moduleTag} succeeded.`;
    this.uninstallModuleSuccessMessage = (moduleTag: string) => `Uninstall action on module ${moduleTag} succeeded.`;

    // Header Selectors
    this.searchModuleTagInput = '#search-input-group input.pstaggerAddTagInput';
    this.searchModuleButton = '#module-search-button';

    // Top menu
    this.topMenuDiv = 'div.module-top-menu';
    this.bulkActionsButton = `${this.topMenuDiv} div.module-top-menu-item:nth-child(3)`;
    this.bulkActionsDropDownButton = '#bulk-actions-dropdown';
    this.bulkActionsDropDownList = 'div.ps-dropdown-menu.dropdown-menu.module-category-selector.items-list.js-items-list.show';
    this.bulkActionName = (action: string) => `${this.bulkActionsDropDownList} a[data-display-name='${action}']`;

    // Bulk actions modal
    this.bulkActionsModal = '#module-modal-bulk-confirm';
    this.bulkActionsModalConfirmButton = '#module-modal-confirm-bulk-ack';

    // Filter by categories dropdown selectors
    this.categoriesSelectDiv = '#categories';
    this.categoriesDropdownDiv = 'div.ps-dropdown-menu.dropdown-menu.module-category-selector';
    this.categoryDropdownItem = (cat: string) => `${this.categoriesDropdownDiv} a[data-category-display-name='${cat}']`;

    // Filter by status dropdown selectors
    this.statusDropdownDiv = '#module-status-dropdown';
    this.statusDropdownMenu = 'div.ps-dropdown-menu[aria-labelledby=\'module-status-dropdown\']';
    this.statusDropdownItemLink = (ref: number) => `${this.statusDropdownMenu} a[data-status-ref='${ref}']`;
    this.filterByAllModulesButton = '.module-status-reset';

    // Modules list selectors
    this.modulesListBlock = '.module-short-list:not([style=\'display: none;\'])';
    this.modulesListBlockTitle = `${this.modulesListBlock} span.module-search-result-title`;
    this.allModulesBlock = `${this.modulesListBlock} .module-item-list`;
    this.moduleBlocks = 'div.module-short-list';
    this.moduleBlock = (moduleTag: string) => `${this.allModulesBlock}[data-tech-name=${moduleTag}]`;
    this.moduleCheckboxButton = (moduleTag: string) => `${this.moduleBlock(moduleTag)}`
      + ' div.module-checkbox-bulk-list.md-checkbox label i';

    // Module actions selector
    this.actionModuleButton = (moduleTag: string, action: string) => `div[data-tech-name=${moduleTag}]`
      + ` button.module_action_menu_${action}`;
    this.configureModuleButton = (moduleTag: string) => `div[data-tech-name=${moduleTag}]`
      + ' div.module-actions a[href*=\'/action/configure\']';

    // Module actions in dropdown selectors
    this.actionsDropdownButton = (moduleTag: string) => `div[data-tech-name=${moduleTag}] button.dropdown-toggle`;
    this.actionModuleButtonInDropdownList = (action: string) => 'div.btn-group.module-actions.show'
      + ` button.module_action_menu_${action}`;

    // Modal confirmation selectors
    this.modalConfirmAction = (moduleTag: string) => `#module-modal-confirm-${moduleTag}`;
    this.modalConfirmButton = (moduleTag: string, action: string) => `${this.modalConfirmAction(moduleTag)}-${action}`
      + ` div.modal-footer a.module_action_modal_${action}`;
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
    await this.reloadPage(page);
    await page.type(this.searchModuleTagInput, module.tag);
    await page.click(this.searchModuleButton);

    return this.elementVisible(page, this.moduleBlock(module.tag), 10000);
  }

  /**
   * Is bulk actions button disabled
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isBulkActionsButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.bulkActionsButton}.disabled`, 1000);
  }

  /**
   * Select module
   * @param page {Page} Browser tab
   * @param moduleTag {string} Technical name of the module
   * @return {Promise<void>}
   */
  async selectModule(page: Page, moduleTag: string): Promise<void> {
    await page.$eval(this.moduleCheckboxButton(moduleTag), (el: HTMLElement) => el.click());
  }

  /**
   * Bulk actions
   * @param page {Page} Browser tab
   * @param action {string} Action to set with bulk actions
   * @return {Promise<string | null>}
   */
  async bulkActions(page: Page, action: string): Promise<string | null> {
    await this.closeGrowlMessage(page);

    await page.click(this.bulkActionsDropDownButton);
    await this.waitForSelectorAndClick(page, this.bulkActionName(action));

    await this.waitForVisibleSelector(page, this.bulkActionsModal);
    await this.waitForSelectorAndClick(page, this.bulkActionsModalConfirmButton);
    return this.getGrowlMessageContent(page);
  }

  /**
   * Click on button configure of a module
   * @param page {Page} Browser tab
   * @param moduleTag {string} Technical name of the module
   * @return {Promise<void>}
   */
  async goToConfigurationPage(page: Page, moduleTag: string): Promise<void> {
    if (await this.elementNotVisible(page, this.configureModuleButton(moduleTag), 1000)) {
      await Promise.all([
        page.click(this.actionsDropdownButton(moduleTag)),
        this.waitForVisibleSelector(page, `${this.actionsDropdownButton(moduleTag)}[aria-expanded='true']`),
      ]);
    }
    await page.click(this.configureModuleButton(moduleTag));
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
   * Get status of module (enable/disable/installed/uninstalled)
   * @param page {Page} Browser tab
   * @param moduleTag {string} Technical name of the module
   * @param action {string} Status of the module to get
   * @return {Promise<boolean>}
   */
  async isModuleStatus(page: Page, moduleTag: string, action: string): Promise<boolean> {
    return this.elementNotVisible(page, this.actionModuleButton(moduleTag, action), 1000);
  }

  /**
   * Get all modules status
   * @param page {Page} Browser tab
   * @param statusToFilterBy {string} Status to filter by
   * @returns {Promise<Array<{ name: string, status: number }[]>>}
   */
  async getAllModulesStatus(page: Page, statusToFilterBy: string): Promise<{ name: string, status: boolean }[]> {
    const modulesStatus: { name: string, status: boolean }[] = [];
    const allModulesTechNames = await this.getAllModulesTechNames(page);

    for (let i = 0; i < allModulesTechNames.length; i++) {
      const moduleTag: string | null = allModulesTechNames[i];

      if (typeof moduleTag === 'string') {
        const moduleStatus = await this.isModuleStatus(page, moduleTag, statusToFilterBy);
        modulesStatus.push({name: moduleTag, status: moduleStatus});
      }
    }

    return modulesStatus;
  }

  /**
   * Get All modules names
   * @param page {Page} Browser tab
   * @return {Promise<Array<string>>}
   */
  async getAllModulesTechNames(page: Page): Promise<(string | null)[]> {
    return page.$$eval(
      this.allModulesBlock,
      (all) => all.map((el) => el.getAttribute('data-tech-name')),
    );
  }

  /**
   * Uninstall/install/enable/disable/reset module
   * @param page {Page} Browser tab
   * @param module {ModuleData} Module data to install/uninstall
   * @param action {string} Action install/uninstall/enable/disable/reset
   * @return {Promise<string | null>}
   */
  async setActionInModule(page: Page, module: ModuleData, action: string): Promise<string | null> {
    await this.closeGrowlMessage(page);

    if (await this.elementVisible(page, this.actionModuleButton(module.tag, action), 1000)) {
      await this.waitForSelectorAndClick(page, this.actionModuleButton(module.tag, action));
      if (action === 'disable' || action === 'uninstall' || action === 'reset') {
        await this.waitForSelectorAndClick(page, this.modalConfirmButton(module.tag, action));
      }
    } else {
      await page.click(this.actionsDropdownButton(module.tag));
      await this.waitForVisibleSelector(page, `${this.actionsDropdownButton(module.tag)}[aria-expanded='true']`);
      await this.waitForSelectorAndClick(page, this.actionModuleButtonInDropdownList(action));
      if (action === 'disable' || action === 'uninstall' || action === 'reset') {
        await this.waitForSelectorAndClick(page, this.modalConfirmButton(module.tag, action));
      }
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
