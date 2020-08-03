require('module-alias/register');
const {expect} = require('chai');
const testContext = require('@utils/testContext');
const loginPage = require('@pages/BO/login');
const dashboardPage = require('@pages/BO/dashboard');

module.exports = {
  async loginBO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginBO');
    await loginPage.goTo(page, global.BO.URL);
    await loginPage.login(page, global.BO.EMAIL, global.BO.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await dashboardPage.closeOnboardingModal(page);
  },

  async logoutBO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');
    await dashboardPage.logoutBO(page);
    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};
