require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewSQLQuery extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryResultTitle = '#main-div  div.card-header h3';
    this.tableColumnName = '#main-div .table th:nth-child(%ID)';
  }

  /*
  Methods
   */
  getSQLQueryResultNumber() {
    return this.getNumberFromText(this.sqlQueryResultTitle);
  }

  getColumnName(id = 1) {
    return this.getTextContent(this.tableColumnName.replace('%ID', id));
  }
};
