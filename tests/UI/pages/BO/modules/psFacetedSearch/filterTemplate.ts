import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_facetedsearch, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsFacetedSearchFilterTemplate extends ModuleConfiguration {
  public readonly title: string;

  private readonly panelTitle: string;

  public readonly submitFilter: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    this.title = 'New filters template';

    // Selectors
    this.panelTitle = '#content .panel h3';
    this.submitFilter = '#submit-filter';
  }

  /* Methods */
  /**
   * Returns the panel title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPanelTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.panelTitle);
  }

  /**
   * Returns the panel title
   * @param page {Page} Browser tab
   * @param filterName {string} Filter Name
   * @param status {boolean} Status
   * @param filterType {string} Filter Type (checkbox, radio, dropdown)
   * @returns {Promise<string>}
   */
  async setFilterForm(page: Page, filterName: string, status: boolean, filterType: string = ''): Promise<string> {
    let selectorStatus: string;
    let selectorFilterType: string;

    switch (filterName) {
      case 'Product brand filter':
        selectorStatus = 'layered_selection_manufacturer';
        break;
      default:
        throw new Error(`The filter "${filterName}" has no defined selector.`);
    }
    await this.setChecked(page, `#${selectorStatus}`, status);

    if (filterType !== '') {
      switch (filterType) {
        case 'checkbox':
          selectorFilterType = 'Checkbox';
          break;
        case 'radio':
          selectorFilterType = 'Radio button';
          break;
        case 'dropdown':
          selectorFilterType = 'Drop-down list';
          break;
        default:
          throw new Error(`The filter type "${filterType}" has no defined selector.`);
      }
      await this.selectByVisibleText(page, `select[name="${selectorStatus}_filter_type"]`, selectorFilterType);
    }

    await this.clickAndWaitForURL(page, this.submitFilter);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsFacetedSearchFilterTemplate();
