require('module-alias/register');
const {expect} = require('chai');
const testContext = require('@utils/testContext');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');

module.exports = {
  async loginFO(mochaContext, page, customer) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginFO');
    await loginPage.goTo(page, global.FO.URL);
    await myAccountPage.goToLoginPage(page);
    await loginPage.customerLogin(page, customer);
    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.contains(myAccountPage.pageTitle);
  },

  async logoutFO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');
    await loginPage.goToMyAccountPage(page);
    await loginPage.logout(page);
    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is still connected').to.be.false;
  },
};
