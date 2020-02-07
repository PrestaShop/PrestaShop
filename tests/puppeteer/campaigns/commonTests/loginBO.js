const {expect} = require('chai');
const testContext = require('@utils/testContext');

module.exports = {
  loginBO() {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO');
      await this.pageObjects.loginPage.goTo(global.BO.URL);
      await this.pageObjects.loginPage.login(global.BO.EMAIL, global.BO.PASSWD);
      const pageTitle = await this.pageObjects.dashboardPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.dashboardPage.pageTitle);
      await this.pageObjects.boBasePage.closeOnboardingModal();
    });
  },

  logoutBO() {
    it('should log out from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO');
      await this.pageObjects.boBasePage.logoutBO();
      const pageTitle = await this.pageObjects.loginPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
    });
  },
};
