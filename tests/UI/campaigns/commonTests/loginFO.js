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
    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.contains(myAccountPage.pageTitle);
  },

  async logoutFO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');
    await loginPage.goToMyAccountPage(page);
    await loginPage.logout(page);
    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};
