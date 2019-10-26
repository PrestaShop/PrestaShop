const {expect} = require('chai');

module.exports = {
  goToBrandsPage() {
    it('should go to brands page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.productsPsarentLink,
        this.pageObjects.boBasePage.brandsAndSuppliersLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
    });
  },
};
