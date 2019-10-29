const {expect} = require('chai');

module.exports = {
  goToPagesPage() {
    it('should go to "Design>Pages" page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.designParentLink,
        this.pageObjects.boBasePage.pagesLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
  },
};
