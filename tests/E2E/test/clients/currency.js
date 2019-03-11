const CommonClient = require('./common_client');
global.checkCurrencyName = [];

class Currency extends CommonClient {

  clickOnAction(actionSelector, groupActionSelector = '', action = 'edit', confirmDelete = true) {
    if (action === 'delete') {
      if (confirmDelete) {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForExistAndClick(actionSelector)
          .alertAccept();
      } else {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForExistAndClick(actionSelector)
          .alertDismiss();
      }
    } else {
      return this.client
        .pause(2000)
        .waitForExistAndClick(actionSelector);
    }
  }
}

module.exports = Currency;
