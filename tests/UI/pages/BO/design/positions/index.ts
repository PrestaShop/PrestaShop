import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Positions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Positions extends BOBasePage {
  public readonly pageTitle: string;

  private readonly searchInput: string;

  private readonly modulePositionForm: string;

  private readonly searchResultHookNameSpan: string;

  private readonly filterModuleButton: string;

  private readonly filterModuleInputValue: string;

  private readonly filterModuleFirstResult: string;

  private readonly modulePositionFormHookSection: string;

  private readonly modulePositionFormHookSectionVisible: string;

  private readonly hookRowNth: (hookRow: number) => string;

  private readonly hookHeader: (hookRow: number) => string;

  private readonly hookHeaderStatusInput: (hookRow: number) => string;

  private readonly hookHeaderNameSpan: (hookRow: number) => string;

  private readonly hookHeaderDescriptionDiv: (hookRow: number) => string;

  private readonly hookRowName: (hookName: string) => string;

  private readonly hookRowModulesList: (hookName: string) => string;

  private readonly hookNameSpan: (hookName: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = `Module positions • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
    this.filterModuleButton = '#position-filters span.select2';
    this.filterModuleInputValue = 'body span.select2-search.select2-search--dropdown input';
    this.filterModuleFirstResult = '#select2-show-modules-results li:nth-child(1)';
    this.modulePositionFormHookSection = `${this.modulePositionForm} section`;
    this.modulePositionFormHookSectionVisible = `${this.modulePositionFormHookSection}.hook-panel.hook-visible`;
    this.hookRowNth = (hookRow: number) => `${this.modulePositionFormHookSection}:nth-child(${hookRow + 1
    } of .hook-panel.hook-visible)`;
    this.hookHeader = (hookRow: number) => `${this.hookRowNth(hookRow)} header`;
    this.hookHeaderStatusInput = (hookRow: number) => `${this.hookHeader(hookRow)} span.hook-status input.hook-switch-action`;
    this.hookHeaderNameSpan = (hookRow: number) => `${this.hookHeader(hookRow)} span.hook-name`;
    this.hookHeaderDescriptionDiv = (hookRow: number) => `${this.hookHeader(hookRow)} div.hook_description`;
    this.hookRowName = (hookName: string) => `${this.modulePositionFormHookSection} a[name=${hookName}]`;
    this.hookRowModulesList = (hookName: string) => `${this.hookRowName(hookName)} ~ section.module-list ul`;
    this.hookNameSpan = (hookName: string) => `${this.hookRowName(hookName)} + header span.hook-name`;
  }

  /* Methods */

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
}

export default new Positions();
