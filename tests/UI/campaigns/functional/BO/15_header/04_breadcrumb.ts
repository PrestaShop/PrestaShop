// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boCustomersPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_breadcrumb';

describe('BO - Header : Breadcrumb', async () => {
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

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Breadcrumb on Orders page', async () => {
    it('should go to the Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should check that the breadcrumb is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbVisibleOrders', baseContext);

      const isBreadcrumbVisible = await boOrdersPage.elementVisible(page, 'nav[aria-label="Breadcrumb"]');
      expect(isBreadcrumbVisible).to.eq(true);
    });

    it('should check the breadcrumb container item', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbContainerOrders', baseContext);

      const breadcrumbContainerText = await boOrdersPage.getTextContent(
        page,
        '.header-toolbar nav .breadcrumb-item:first-child',
      );
      expect(breadcrumbContainerText).to.contains('Sell');
    });

    it('should check the breadcrumb tab item', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbTabOrders', baseContext);

      const breadcrumbTabText = await boOrdersPage.getTextContent(
        page,
        '.header-toolbar nav .breadcrumb-item.active',
      );
      expect(breadcrumbTabText).to.contains(boOrdersPage.pageTitle);
    });
  });

  describe('Breadcrumb on Customers page', async () => {
    it('should go to the Customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );
      await boCustomersPage.closeSfToolBar(page);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should check that the breadcrumb is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbVisibleCustomers', baseContext);

      const isBreadcrumbVisible = await boCustomersPage.elementVisible(page, 'nav[aria-label="Breadcrumb"]');
      expect(isBreadcrumbVisible).to.eq(true);
    });

    it('should check the breadcrumb container item', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbContainerCustomers', baseContext);

      const breadcrumbContainerText = await boCustomersPage.getTextContent(
        page,
        '.header-toolbar nav .breadcrumb-item:first-child',
      );
      expect(breadcrumbContainerText).to.contains('Sell');
    });

    it('should check the breadcrumb tab item', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbTabCustomers', baseContext);

      const breadcrumbTabText = await boCustomersPage.getTextContent(
        page,
        '.header-toolbar nav .breadcrumb-item.active',
      );
      expect(breadcrumbTabText).to.contains(boCustomersPage.pageTitle);
    });
  });
});
