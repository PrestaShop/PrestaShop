import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Positions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Positions extends BOBasePage {
  public readonly pageTitle: string;

  public readonly messageModuleAddedFromHook: string;

  public readonly messageModuleRemovedFromHook: string;

  private readonly headerButtonHookModule: string;

  private readonly searchInput: string;

  private readonly modulePositionForm: string;

  private readonly searchResultHookNameSpan: string;

  private readonly filterModuleSelect: string;

  private readonly filterModuleButton: string;

  private readonly filterModuleInputValue: string;

  private readonly filterModuleFirstResult: string;

  private readonly modulePositionFormHookSection: string;

  private readonly modulePositionFormHookSectionHook: (hookName: string) => string;

  private readonly modulePositionFormHookSectionVisible: string;

  private readonly modulePositionFormHookSectionHookModule: (hookName: string, moduleName: string) => string;

  private readonly hookRowNth: (hookRow: number) => string;

  private readonly hookHeader: (hookRow: number) => string;

  private readonly hookHeaderStatusInput: (hookRow: number) => string;

  private readonly hookHeaderNameSpan: (hookRow: number) => string;

  private readonly hookHeaderDescriptionDiv: (hookRow: number) => string;

  private readonly hookRowName: (hookName: string) => string;

  private readonly hookRowModulesList: (hookName: string) => string;

  private readonly hookNameSpan: (hookName: string) => string;

  private readonly selectionPanel: string;

  private readonly selectionPanelSingle: string;

  private readonly selectionPanelButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = `Module positions • ${global.INSTALL.SHOP_NAME}`;
    this.messageModuleAddedFromHook = 'The module transplanted successfully to the hook.';
    this.messageModuleRemovedFromHook = 'The module was successfully removed from the hook.';

    // Selectors
    this.headerButtonHookModule = '#page-header-desc-configuration-save';
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
    this.filterModuleSelect = '#show-modules';
    this.filterModuleButton = '#position-filters span.select2';
    this.filterModuleInputValue = 'body span.select2-search.select2-search--dropdown input';
    this.filterModuleFirstResult = '#select2-show-modules-results li:nth-child(1)';
    this.modulePositionFormHookSection = `${this.modulePositionForm} section`;
    this.modulePositionFormHookSectionHook = (hookName: string) => `${this.modulePositionFormHookSection}`
       + `[data-hook-name="${hookName}"]`;
    this.modulePositionFormHookSectionVisible = `${this.modulePositionFormHookSection}.hook-panel.hook-visible`;
    this.modulePositionFormHookSectionHookModule = (hookName: string, moduleName: string) => `${
      this.modulePositionFormHookSectionHook(hookName)} input[data-hook-module="${moduleName}"]`;
    this.hookRowNth = (hookRow: number) => `${this.modulePositionFormHookSection}:nth-child(${hookRow + 1
    } of .hook-panel.hook-visible)`;
    this.hookHeader = (hookRow: number) => `${this.hookRowNth(hookRow)} header`;
    this.hookHeaderStatusInput = (hookRow: number) => `${this.hookHeader(hookRow)} span.hook-status input.hook-switch-action`;
    this.hookHeaderNameSpan = (hookRow: number) => `${this.hookHeader(hookRow)} span.hook-name`;
    this.hookHeaderDescriptionDiv = (hookRow: number) => `${this.hookHeader(hookRow)} div.hook_description`;
    this.hookRowName = (hookName: string) => `${this.modulePositionFormHookSection} a[name=${hookName}]`;
    this.hookRowModulesList = (hookName: string) => `${this.hookRowName(hookName)} ~ section.module-list ul`;
    this.hookNameSpan = (hookName: string) => `${this.hookRowName(hookName)} + header span.hook-name`;

    // Selection panel
    this.selectionPanel = '#modules-position-selection-panel';
    this.selectionPanelSingle = '#modules-position-single-selection';
    this.selectionPanelButton = `${this.selectionPanel} .card-body button`;
  }

  /* Methods */
  /**
   * Click on Header button "Hook a module"
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickHeaderHookModule(page: Page): Promise<void> {
    await page.locator(this.headerButtonHookModule).click();
  }

  /**
   * Search for a hook
   * @param page {Page} Browser tab
   * @param hookValue {string} Value of hook to set on input
   * @returns {Promise<string>}
   */
  async searchHook(page: Page, hookValue: string): Promise<string> {
    await this.setValue(page, this.searchInput, hookValue);

    return this.getTextContent(page, this.searchResultHookNameSpan);
  }

  /**
   * Filter module
   * @param page {Page} Browser tab
   * @param moduleName {string} Module name to filter by
   * @returns {Promise<void>}
   */
  async filterModule(page: Page, moduleName: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.filterModuleButton);
    await this.setValue(page, this.filterModuleInputValue, moduleName);
    await this.waitForSelectorAndClick(page, this.filterModuleFirstResult);
  }

  /**
   * Return the filtered module name
   * @param page
   */
  async getModuleFilter(page: Page): Promise<string> {
    return this.getTextContent(page, `${this.filterModuleSelect} option[selected='selected']`, false);
  }

  /**
   * Get number of hooks
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfHooks(page: Page): Promise<number> {
    return page.locator(this.modulePositionFormHookSectionVisible).count();
  }

  /**
   * Get modules in hook
   * @param page {Page} Browser tab
   * @param hookName {string} Hook name
   * @returns {Promise<string>}
   */
  async getModulesInHook(page: Page, hookName: string): Promise<string> {
    return this.getTextContent(page, this.hookRowModulesList(hookName));
  }

  /**
   * Is hook visible
   * @param page {Page} Browser tab
   * @param hookName {string} Hook name
   * @returns {Promise<boolean>}
   */
  async isHookVisible(page: Page, hookName: string): Promise<boolean> {
    return this.elementVisible(page, this.hookNameSpan(hookName), 1000);
  }

  /**
   * Return the hookId
   * @param page {Page} Browser tab
   * @param hookRow {number} Hook Row
   * @returns {Promise<boolean>}
   */
  async getHookId(page: Page, hookRow: number): Promise<number> {
    const attribute = await this.getAttributeContent(page, this.hookHeaderStatusInput(hookRow), 'data-hook-id');

    return parseInt(attribute, 10);
  }

  /**
   * Return the hook name
   * @param page {Page} Browser tab
   * @param hookRow {number} Hook Row
   * @returns {Promise<string>}
   */
  async getHookName(page: Page, hookRow: number): Promise<string> {
    return this.getTextContent(page, this.hookHeaderNameSpan(hookRow));
  }

  /**
   * Return the hook description
   * @param page {Page} Browser tab
   * @param hookRow {number} Hook Row
   * @returns {Promise<string>}
   */
  async getHookDescription(page: Page, hookRow: number): Promise<string> {
    return this.getTextContent(page, this.hookHeaderDescriptionDiv(hookRow));
  }

  /**
   * Return the hook status
   * @param page {Page} Browser tab
   * @param hookRow {number} Hook Row
   * @returns {Promise<boolean>}
   */
  async getHookStatus(page: Page, hookRow: number): Promise<boolean> {
    const inputValue = await this.getAttributeContent(page, `${this.hookHeaderStatusInput(hookRow)}:checked`, 'value');

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Select a module hooked on a hook and display the selection box
   * @param page {Page} Browser tab
   * @param hookName {number} Hook Name
   * @param moduleName {number} Module Name
   * @returns {Promise<boolean>}
   */
  async selectHookModule(page: Page, hookName: string, moduleName: string): Promise<boolean> {
    await this.setHiddenCheckboxValue(page, this.modulePositionFormHookSectionHookModule(hookName, moduleName), true);

    return this.elementVisible(page, this.selectionPanel, 3000);
  }

  /**
   * Return the number of selected hooks
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getSelectedHookCount(page: Page): Promise<number> {
    if (await this.elementVisible(page, this.selectionPanelSingle, 3000)) {
      return 1;
    }
    // @todo
    return 0;
  }

  /**
   * Unhook selection and get return message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async unhookSelection(page: Page): Promise<string> {
    await page.locator(this.selectionPanelButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Positions();
