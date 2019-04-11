const CommonClient = require('./common_client');
global.checkCurrencyName = [];

class Currency extends CommonClient {

  async clickOnAction(actionSelector, groupActionSelector = '', action = 'edit', confirmDelete = true) {
    if (action === 'delete') {
      if (confirmDelete) {
        await this.waitForExistAndClick(groupActionSelector);
        await this.alertAccept();

        await this.waitForExistAndClick(actionSelector);
      } else {
        await this.waitForExistAndClick(groupActionSelector);
        await this.alertAccept('dismiss');
        await this.waitForExistAndClick(actionSelector);
      }
    } else {
      await this.pause(2000);
      await this.waitForExistAndClick(actionSelector);
    }
  }

  getCurrencyNumber(selector, globalVar, timeout = 90000) {
    return this.client
      .waitForExist(selector, timeout)
      .then(() => this.client.getText(selector))
      .then((variable) => {
        global.tab[globalVar] = (variable.split('(')[1]).split(')')[0];
      });
  }
}

module.exports = Currency;
