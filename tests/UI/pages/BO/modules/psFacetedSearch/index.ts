import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';
import psFacetedSearchFilterTemplatePage from '@pages/BO/modules/psFacetedSearch/filterTemplate';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_facetedsearch, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsFacetedSearch extends ModuleConfiguration {
  public readonly pageSubTitle: string;

  private readonly gridTable: string;

  private readonly gridTableBody: string;

  private readonly gridTableBodyRows: string;

  private readonly gridTableBodyRow: (row: number) => string;

  private readonly gridTableBodyColumn: (row: number) => string;

  private readonly gridTableColumnActions: (row: number) => string;

  private readonly gridTableColumnActionsEditLink: (row: number) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    this.pageSubTitle = 'Faceted search';

    this.gridTable = 'table.table';
    this.gridTableBody = `${this.gridTable} tbody`;
    this.gridTableBodyRows = `${this.gridTableBody} tr`;
    this.gridTableBodyRow = (row: number) => `${this.gridTableBodyRows}:nth-child(${row})`;
    this.gridTableBodyColumn = (row: number) => `${this.gridTableBodyRow(row)} td`;
    this.gridTableColumnActions = (row: number) => `${this.gridTableBodyColumn(row)} .btn-group-action`;
    this.gridTableColumnActionsEditLink = (row: number) => `${this.gridTableColumnActions(row)} a.btn`;
  }

  /* Methods */
  /**
   *
   * @param page {Page} Browser tab
   * @param row {number} Row number
   * @returns {Promise<number>}
   */
  async editFilterTemplate(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForLoadState(page, this.gridTableColumnActionsEditLink(row));
    await this.elementVisible(page, psFacetedSearchFilterTemplatePage.submitFilter);
  }
}

export default new PsFacetedSearch();
