require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewSQLQuery extends BOBasePage {
  constructor(page) {
    super(page);

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
   * @returns {integer}
   */
  getSQLQueryResultNumber() {
    return this.getNumberFromText(this.sqlQueryResultTitle);
  }

  /**
   * Get columns name
   * @param id
   * @returns {Promise<string>}
   */
  getColumnName(id = 1) {
    return this.getTextContent(this.tableColumnName(id));
  }
};
