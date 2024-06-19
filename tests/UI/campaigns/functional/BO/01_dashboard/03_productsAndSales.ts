// Import utils
import testContext from '@utils/testContext';

// Import BO pages
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_dashboard_productsAndSales';

describe('BO - Dashboard : Products and sales', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check Online visitor & Active shopping carts', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Check Recent orders tab', async () => {
      it('should check the title of Recent orders tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkRecentOrdersTitle', baseContext);

        const tableTitle = await boDashboardPage.getRecentOrdersTitle(page);
        expect(tableTitle).to.eq('Last 10 orders');
      });

      it('should click on details icon of the first row and check Order details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailsButton', baseContext);

        await boDashboardPage.clickOnDetailsButtonOfRecentOrdersTable(page, 1);

        const pageTitle = await viewOrderBasePage.getPageTitle(page);
        expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard', baseContext);

        await viewOrderBasePage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });
    });

    describe('Check Best sellers tab', async () => {
      it('should click on best sellers tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnBestSellersTab', baseContext);

        await boDashboardPage.goToBestSellersTab(page);

        const tabTitle = await boDashboardPage.getBestSellersTabTitle(page);
        expect(tabTitle).to.contains('Top 10 products');
      });

      it('should check that the best sellers table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isBestSellersTableVisible', baseContext);

        const isVisible = await boDashboardPage.isBestSellersTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Check Most viewed tab', async () => {
      it('should click on most viewed tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnMostViewedTab', baseContext);

        await boDashboardPage.goToMostViewedTab(page);

        const tabTitle = await boDashboardPage.getMostViewedTabTitle(page);
        expect(tabTitle).to.contains('Most Viewed');
      });

      it('should check that the most viewed table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isMostViewedTableVisible', baseContext);

        const isVisible = await boDashboardPage.isMostViewedTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Check Top searches tab', async () => {
      it('should check top searchers tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTopSearchersTab', baseContext);

        await boDashboardPage.goToTopSearchersTab(page);

        const tabTitle = await boDashboardPage.getTopSearchersTabTitle(page);
        expect(tabTitle).to.contains('Top 10 most search terms');
      });

      it('should check that the top searchers table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isTopSearchersTableVisible', baseContext);

        const isVisible = await boDashboardPage.isTopSearchersTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Configuration', async () => {
      it('should click on configure link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnConfigureLink', baseContext);

        const isConfigureFormVisible = await boDashboardPage.clickOnConfigureProductsAndSalesLink(page);
        expect(isConfigureFormVisible).to.eq(true);
      });

      // @todo https://github.com/PrestaShop/PrestaShop/issues/34326
    });
  });
});
