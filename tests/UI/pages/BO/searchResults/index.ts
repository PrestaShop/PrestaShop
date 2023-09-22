import BOBasePage from '@pages/BO/BObasePage';
import type {Page} from 'playwright';

/**
 * Search page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class SearchResults extends BOBasePage {
  public readonly pageTitle: string;

  private readonly contentDiv: string;

  private readonly headerTitle: string;

  private readonly typeDiv: (type: string) => string;

  private readonly typeTable: (type: string) => string;

  private readonly typeTableColumn: (type: string, row: number, column: number) => string;

  private readonly typeHeaderTitle: (type: string) => string;

  private readonly searchPanels: string;

  private readonly searchPanelsLinks: string;

  private readonly searchPanelsLink: (nth: number) => string;

  private readonly allowedTypes: {
    categories: RegExp,
    customers: RegExp,
    features: RegExp,
    modules: RegExp,
    orders: RegExp,
    products: RegExp,
  };

  /**
   * @constructs
   * Setting up texts and selectors to use on search page
   */
  constructor() {
    super();

    this.pageTitle = 'Search results •';

    // Selectors
    this.contentDiv = '#content';
    this.headerTitle = `${this.contentDiv} h2`;
    this.typeDiv = (type: string) => `${this.contentDiv} div.panel[data-role="${type}"]`;
    this.typeTable = (type: string) => `${this.typeDiv(type)} table`;
    this.typeTableColumn = (type: string, row: number, column: number) => `${this.typeTable(type)} tbody tr:nth-of-type(${row}) `
      + `td:nth-of-type(${column})`;
    this.typeHeaderTitle = (type: string) => `${this.typeDiv(type)} h3`;
    this.searchPanels = `${this.contentDiv} div[data-role="search-panels"]`;
    this.searchPanelsLinks = `${this.searchPanels} a`;
    this.searchPanelsLink = (nth: number) => `${this.searchPanelsLinks}:nth-of-type(${nth})`;

    /**
     * @private
     * @type {{
     *   categories: RegExp,
     *   customers: RegExp,
     *   features: RegExp,
     *   modules: RegExp,
     *   orders: RegExp,
     *   products: RegExp
     * }}
     */
    this.allowedTypes = {
      categories: /([0-9]+) categor[y|ies]+/,
      customers: /([0-9]+) customer[s]{0,1}/,
      features: /([0-9]+) feature[s]{0,1}/,
      modules: /([0-9]+) module[s]{0,1}/,
      orders: /([0-9]+) order[s]{0,1}/,
      products: /([0-9]+) product[s]{0,1}/,
    };
  }

  /* Methods */
  /**
   * Get number of results (in global or in a specific type)
   * @param page {Page} Browser tab
   * @param type {string} Type of results wanted
   * @returns {Promise<number>}
   */
  async getNumberResults(page: Page, type: string = ''): Promise<number> {
    if (type === '') {
      const headerTitle = await this.getTextContent(page, this.headerTitle);
      const regexpResultsHeader: RegExpMatchArray|null = headerTitle.match(/([0-9]+) result[s]{0,1} match[es]{0,2} your query /);
      const resultsHeader: RegExpExecArray|null = /\d+/g.exec(regexpResultsHeader ? regexpResultsHeader[0] : '');
      const numberResultsHeader = resultsHeader === null ? '0' : resultsHeader.toString();

      return parseInt(numberResultsHeader, 10);
    }

    if (!Object.keys(this.allowedTypes).includes(type)) {
      throw new Error(`${type} has not been found in allowed types : ${Object.keys(this.allowedTypes).join(', ')}`);
    }
    let typeHeader: RegExp|null = null;

    Object.entries(this.allowedTypes).find(([key, value]) => {
      if (key === type) {
        typeHeader = value;
        return true;
      }

      return false;
    });

    if (typeHeader === null) {
      throw new Error(`${type} is not a RegExp`);
    }

    const typeHeaderTitle = await this.getTextContent(page, this.typeHeaderTitle(type));
    const regexpResultsTypeHeader: RegExpMatchArray|null = typeHeaderTitle.match(typeHeader);
    const resultsTypeHeader: RegExpExecArray|null = /\d+/g.exec(regexpResultsTypeHeader ? regexpResultsTypeHeader[0] : '');
    const numberResultsTypeHeader = resultsTypeHeader === null ? '0' : resultsTypeHeader.toString();

    return parseInt(numberResultsTypeHeader, 10);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getSearchPanelsLinksNumber(page: Page): Promise<number> {
    return (await page.$$(this.searchPanelsLinks)).length;
  }

  /**
   * Return the link URL in Search panels
   * @param page {Page} Browser tab
   * @param nthLink {number} Nth link
   * @returns {Promise<string>}
   */
  async getSearchPanelsLinkURL(page: Page, nthLink: number): Promise<string> {
    return this.getAttributeContent(page, this.searchPanelsLink(nthLink), 'href');
  }

  /**
   * Return the link Text in Search panels
   * @param page {Page} Browser tab
   * @param nthLink {int} Nth link
   * @returns {Promise<string>}
   */
  async getSearchPanelsLinkText(page: Page, nthLink: number): Promise<string> {
    return this.getTextContent(page, this.searchPanelsLink(nthLink));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param type {string} Type of results wanted
   * @param row {number} Row index in the table
   * @param columnName {string} Column name in the table
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, type: string, row: number, columnName: string): Promise<string> {
    let columnSelector;

    switch (type) {
      case 'customers':
        switch (columnName) {
          case 'firstname':
            columnSelector = this.typeTableColumn(type, row, 3);
            break;
          case 'name':
            columnSelector = this.typeTableColumn(type, row, 4);
            break;
          default:
            throw new Error(`Column ${columnName} was not found`);
        }
        break;
      default:
        throw new Error(`Table ${type} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }
}

export default new SearchResults();
