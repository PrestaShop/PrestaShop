const {expect} = require('chai');

module.exports = {
  loginBO() {
    it('should login in BO', async function () {
      await this.pageObjects.loginPage.goTo(global.URL_BO);
      await this.pageObjects.loginPage.login(global.EMAIL, global.PASSWD);
      const pageTitle = await this.pageObjects.dashboardPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.dashboardPage.pageTitle);
      await this.pageObjects.boBasePage.closeOnboardingModal();
    });
  },
};
