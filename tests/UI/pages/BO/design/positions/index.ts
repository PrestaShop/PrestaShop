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

  private readonly hookNameLink: (hookName: string) => string;

  private readonly modulesListInHookResults: (hookName: string) => string;

  private readonly hookNameSpan: (hookName: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = 'Positions';

    // Selectors
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
    this.filterModuleButton = '#position-filters span.select2';
    this.filterModuleInputValue = 'body span.select2-search.select2-search--dropdown input';
    this.filterModuleFirstResult = '#select2-show-modules-results li:nth-child(1)';
    this.modulePositionFormHookSection = `${this.modulePositionForm} section[style='']`;
    this.hookNameLink = (hookName: string) => `${this.modulePositionFormHookSection} a[name=${hookName}]`;
    this.modulesListInHookResults = (hookName: string) => `${this.hookNameLink(hookName)} ~ section.module-list ul`;
    this.hookNameSpan = (hookName: string) => `${this.hookNameLink(hookName)} + header span.hook-name`;
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
    return (await page.$$(this.modulePositionFormHookSection)).length;
  }

  /**
   * Get modules in hook
   * @param page {Page} Browser tab
   * @param hookName {string} Hook name
   * @returns {Promise<string>}
   */
  async getModulesInHook(page: Page, hookName: string): Promise<string> {
    return this.getTextContent(page, this.modulesListInHookResults(hookName));
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
}

export default new Positions();
