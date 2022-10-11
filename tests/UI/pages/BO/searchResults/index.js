require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Search page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class SearchResults extends BOBasePage {
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
    this.typeDiv = type => `${this.contentDiv} div.panel[data-role="${type}"]`;
    this.typeTable = type => `${this.typeDiv(type)} table`;
    this.typeTableColumn = (type, row, column) => `${this.typeTable(type)} tbody tr:nth-of-type(${row}) `
      + `td:nth-of-type(${column})`;
    this.typeHeaderTitle = type => `${this.typeDiv(type)} h3`;
    this.searchPanels = `${this.contentDiv} div[data-role="search-panels"]`;
    this.searchPanelsLinks = `${this.searchPanels} a`;
    this.searchPanelsLink = nth => `${this.searchPanelsLinks}:nth-of-type(${nth})`;

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
   * @returns {Promise<int>}
   */
  async getNumberResults(page, type = '') {
    if (type === '') {
      const headerTitle = await this.getTextContent(page, this.headerTitle);
      const results = /\d+/g.exec(headerTitle.match(/([0-9]+) result[s]{0,1} match[es]{0,2} your query /));
      const numberResults = results === null ? 0 : results.toString();
      return parseInt(numberResults, 10);
    }

    if (!Object.keys(this.allowedTypes).includes(type)) {
      throw new Error(`${type} has not been found in allowed types : ${Object.keys(this.allowedTypes).join(', ')}`);
    }

    const typeHeaderTitle = await this.getTextContent(page, this.typeHeaderTitle(type));
    const results = /\d+/g.exec(typeHeaderTitle.match(this.allowedTypes[type]));
    const numberResults = results === null ? 0 : results.toString();
    return parseInt(numberResults, 10);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @returns {Promise<*>}
   */
  async getSearchPanelsLinksNumber(page) {
    return (await page.$$(this.searchPanelsLinks)).length;
  }

  /**
   * Return the link URL in Search panels
   * @param page {Page} Browser tab
   * @param nthLink {int} Nth link
   * @returns {Promise<string>}
   */
  async getSearchPanelsLinkURL(page, nthLink) {
    return this.getAttributeContent(page, this.searchPanelsLink(nthLink), 'href');
  }

  /**
   * Return the link Text in Search panels
   * @param page {Page} Browser tab
   * @param nthLink {int} Nth link
   * @returns {Promise<string>}
   */
  async getSearchPanelsLinkText(page, nthLink) {
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
  async getTextColumn(page, type, row, columnName) {
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

module.exports = new SearchResults();
