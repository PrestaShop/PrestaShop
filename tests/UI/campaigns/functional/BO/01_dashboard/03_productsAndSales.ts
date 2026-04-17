import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersViewBasePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_dashboard_productsAndSales';

describe('BO - Dashboard : Products and sales', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check Online visitor & Active shopping carts', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

        const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard', baseContext);

        await boOrdersViewBasePage.goToDashboardPage(page);

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

      it('should change value to 5 and check all tabs', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAllTabsFor5', baseContext);

        await boDashboardPage.setFormProductAndSales(page, 5, 5, 5, 5);

        const titleRecentOrders = await boDashboardPage.getRecentOrdersTitle(page);
        expect(titleRecentOrders).to.equals('Last 5 orders');

        await boDashboardPage.goToBestSellersTab(page);

        const titleBestSellers = await boDashboardPage.getBestSellersTabTitle(page);
        expect(titleBestSellers).to.contains('Top 5 products');

        await boDashboardPage.goToMostViewedTab(page);

        const titleMostViewed = await boDashboardPage.getMostViewedTabTitle(page);
        expect(titleMostViewed).to.contains('Most Viewed');

        await boDashboardPage.goToTopSearchersTab(page);

        const titleTopSearchers = await boDashboardPage.getTopSearchersTabTitle(page);
        expect(titleTopSearchers).to.contains('Top 5 most search terms');
      });

      it('should click on configure link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnConfigureLinkReset', baseContext);

        const isConfigureFormVisible = await boDashboardPage.clickOnConfigureProductsAndSalesLink(page);
        expect(isConfigureFormVisible).to.eq(true);
      });

      it('should change value to 10 and check all tabs', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAllTabsFor10', baseContext);

        await boDashboardPage.setFormProductAndSales(page, 10, 10, 10, 10);

        const titleRecentOrders = await boDashboardPage.getRecentOrdersTitle(page);
        expect(titleRecentOrders).to.equals('Last 10 orders');

        await boDashboardPage.goToBestSellersTab(page);

        const titleBestSellers = await boDashboardPage.getBestSellersTabTitle(page);
        expect(titleBestSellers).to.contains('Top 10 products');

        await boDashboardPage.goToMostViewedTab(page);

        const titleMostViewed = await boDashboardPage.getMostViewedTabTitle(page);
        expect(titleMostViewed).to.contains('Most Viewed');

        await boDashboardPage.goToTopSearchersTab(page);

        const titleTopSearchers = await boDashboardPage.getTopSearchersTabTitle(page);
        expect(titleTopSearchers).to.contains('Top 10 most search terms');
      });
    });
  });
});
