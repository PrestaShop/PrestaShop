require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewSQLQuery extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryResultTitle = '#main-div  div.card-header h3';
    this.tableColumnName = id => `#main-div .table th:nth-child(${id})`;
  }

  /*
  Methods
   */
  /**
   * Get SQL query result number
   * @param page
   * @returns {Promise<number>}
   */
  getSQLQueryResultNumber(page) {
    return this.getNumberFromText(page, this.sqlQueryResultTitle);
  }

  /**
   * Get columns name
   * @param page
   * @param id
   * @returns {Promise<string>}
   */
  getColumnName(page, id = 1) {
    return this.getTextContent(page, this.tableColumnName(id));
  }
}

module.exports = new ViewSQLQuery();
