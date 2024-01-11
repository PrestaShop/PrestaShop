// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_dashboard_productsAndSales';

describe('BO - Dashboard : Products and sales', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check Online visitor & Active shopping carts', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Check Recent orders tab', async () => {
      it('should check the title of Recent orders tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkRecentOrdersTitle', baseContext);

        const tableTitle = await dashboardPage.getRecentOrdersTitle(page);
        expect(tableTitle).to.eq('Last 10 orders');
      });

      it('should click on details icon of the first row and check Order details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailsButton', baseContext);

        await dashboardPage.clickOnDetailsButtonOfRecentOrdersTable(page, 1);

        const pageTitle = await viewOrderBasePage.getPageTitle(page);
        expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard', baseContext);

        await viewOrderBasePage.goToDashboardPage(page);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(dashboardPage.pageTitle);
      });
    });

    describe('Check Best sellers tab', async () => {
      it('should click on best sellers tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnBestSellersTab', baseContext);

        await dashboardPage.goToBestSellersTab(page);

        const tabTitle = await dashboardPage.getBestSellersTabTitle(page);
        expect(tabTitle).to.contains('Top 10 products');
      });

      it('should check that the best sellers table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isBestSellersTableVisible', baseContext);

        const isVisible = await dashboardPage.isBestSellersTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Check Most viewed tab', async () => {
      it('should click on most viewed tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnMostViewedTab', baseContext);

        await dashboardPage.goToMostViewedTab(page);

        const tabTitle = await dashboardPage.getMostViewedTabTitle(page);
        expect(tabTitle).to.contains('Most Viewed');
      });

      it('should check that the most viewed table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isMostViewedTableVisible', baseContext);

        const isVisible = await dashboardPage.isMostViewedTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Check Top searches tab', async () => {
      it('should check top searchers tab and check the title', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTopSearchersTab', baseContext);

        await dashboardPage.goToTopSearchersTab(page);

        const tabTitle = await dashboardPage.getTopSearchersTabTitle(page);
        expect(tabTitle).to.contains('Top 10 most search terms');
      });

      it('should check that the top searchers table is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isTopSearchersTableVisible', baseContext);

        const isVisible = await dashboardPage.isTopSearchersTableVisible(page);
        expect(isVisible).to.equal(true);
      });
    });

    describe('Configuration', async () => {
      it('should click on configure link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnConfigureLink', baseContext);

        const isConfigureFormVisible = await dashboardPage.clickOnConfigureProductsAndSalesLink(page);
        expect(isConfigureFormVisible).to.eq(true);
      });

      // @todo https://github.com/PrestaShop/PrestaShop/issues/34326
    });
  });
});
