// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boWallOfFamePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_wallOfFame_topCompanies';

/*
Pre-condition:
- Login in BO

Scenario:
- Go to Community > Wall of Fame
- Check page title
- Check Top Companies card title
- Check Top Companies card description
- Check Top Companies table column headers
- Click action button for PrestaShop company
- Verify new tab URL contains prestashop.com
- Close new tab and return to Wall of Fame
*/
describe('BO - Community : Wall of Fame', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Wall of Fame page', async () => {
    it('should go to \'Community > Wall of Fame\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToWallOfFamePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        '',
        boDashboardPage.wallOfFameLink,
      );
      await boWallOfFamePage.closeSfToolBar(page);

      const pageTitle = await boWallOfFamePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
    });

    it('should check the Top Companies card title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopCompaniesTitle', baseContext);

      const cardTitle = await boWallOfFamePage.getTopCompaniesCardTitle(page);
      expect(cardTitle).to.contains('Top companies');
    });

    it('should check the Top Companies card description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopCompaniesDescription', baseContext);

      const description = await boWallOfFamePage.getTopCompaniesDescription(page);
      expect(description).to.equal('Meet the top companies who are helping us strengthen PrestaShop.');
    });

    it('should check the Top Companies table column headers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopCompaniesTableHeaders', baseContext);

      const headers = await boWallOfFamePage.getTopCompaniesTableColumnHeaders(page);
      expect(headers).to.include.members(['Rank', 'Logo', 'Name', 'Contributions']);
    });

    it('should click the action button for PrestaShop company and check the opened URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickPrestaShopActionButton', baseContext);

      const newPage = await boWallOfFamePage.clickCompanyActionButton(page, 'PrestaShop');

      const url = newPage.url();
      expect(url, 'Action button should open the PrestaShop website').to.contains('prestashop.com');

      page = await boWallOfFamePage.closePage(browserContext, newPage, 0);
    });

    it('should check Wall of Fame page is still displayed after returning from external link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWallOfFameStillDisplayed', baseContext);

      const pageTitle = await boWallOfFamePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
    });
  });
});
