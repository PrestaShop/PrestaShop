import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesCreatePage,
  boCategoriesCreatePage,
  boCustomersCreatePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boModuleManagerPage,
  boProductsPage,
  boQuickAccessPage,
  boQuickAccessCreatePage,
  boStatisticsPage,
  type BrowserContext,
  FakerQuickAccess,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_quickAccess';

describe('BO - Header : Quick access links', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const quickAccessLinkData: FakerQuickAccess = new FakerQuickAccess({
    name: 'New customer',
    url: 'index.php/sell/customers/new',
    openNewWindow: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check quick access links', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    [
      {pageName: 'Catalog evaluation', pageTitle: boStatisticsPage.pageTitle},
      {pageName: 'Installed modules', pageTitle: boModuleManagerPage.pageTitle},
      {pageName: 'New category', pageTitle: boCategoriesCreatePage.pageTitleCreate},
      {pageName: 'New product', pageTitle: boProductsPage.pageTitle},
      {pageName: 'Orders', pageTitle: boOrdersPage.pageTitle},
      {pageName: 'New voucher', pageTitle: boCartRulesCreatePage.pageTitle},
    ].forEach((test: {pageName: string, pageTitle: string}, index: number) => {
      it(`should check '${test.pageName}' link from Quick access`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkLink${index}`, baseContext);

        if (test.pageName === 'New product') {
          await boDashboardPage.quickAccessToPageWithFrame(page, test.pageName);

          const isModalVisible = await boProductsPage.isNewProductModalVisibleInFrame(page);
          expect(isModalVisible).to.be.equal(true);

          const isModalNotVisible = await boProductsPage.closeNewProductModal(page);
          expect(isModalNotVisible).to.be.equal(true);
        } else {
          await boDashboardPage.quickAccessToPage(page, test.pageName);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(test.pageTitle);
        }
      });
    });

    it('should remove the last link \'New voucher\' from Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeLinkFromQuickAccess', baseContext);

      const validationMessage = await boCartRulesCreatePage.removeLinkFromQuickAccess(page);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it('should refresh the page and add current page to Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCurrentPageToQuickAccess', baseContext);

      await boCartRulesCreatePage.reloadPage(page);

      const validationMessage = await boCartRulesCreatePage.addCurrentPageToQuickAccess(page, 'New voucher');
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it('should go to \'Manage quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToCreateLink', baseContext);

      await boCartRulesCreatePage.reloadPage(page);
      await boCartRulesCreatePage.goToManageQuickAccessPage(page);

      const pageTitle = await boQuickAccessPage.getPageTitle(page);
      expect(pageTitle).to.contains(boQuickAccessPage.pageTitle);
    });

    it('should go to \'Add new quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddQuickAccessPage', baseContext);

      await boQuickAccessPage.goToAddNewQuickAccessPage(page);

      const pageTitle = await boQuickAccessCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boQuickAccessCreatePage.pageTitle);
    });

    it('should create new quick access link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createQuickAccessLink', baseContext);

      const validationMessage = await boQuickAccessCreatePage.setQuickAccessLink(page, quickAccessLinkData);
      expect(validationMessage).to.contains(boQuickAccessCreatePage.successfulCreationMessage);
    });

    it('should check the new link from Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewLink', baseContext);

      const newPage = await boDashboardPage.quickAccessToPageNewWindow(page, quickAccessLinkData.name);

      const pageTitle = await boCustomersCreatePage.getPageTitle(newPage);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);

      // Close the tab opened in a new window and keep working on the original tab.
      // Reading the grid on the freshly-opened tab while several heavy BO tabs stay open makes
      // the later "filter by link name" step flaky (the filtered row renders after the read timeout).
      await newPage.close();
    });

    it('should go to \'Manage quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToDeleteLink', baseContext);

      await boCustomersCreatePage.goToManageQuickAccessPage(page);

      const pageTitle = await boQuickAccessPage.getPageTitle(page);
      expect(pageTitle).to.contains(boQuickAccessPage.pageTitle);
    });

    it('should filter quick access table by link name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchByName', baseContext);

      await boQuickAccessPage.filterTable(page, 'input', 'name', quickAccessLinkData.name);

      const textColumn = await boQuickAccessPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(quickAccessLinkData.name);
    });

    it('should delete the created quick access link by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteByBulkActions', baseContext);

      const textColumn = await boQuickAccessPage.bulkDeleteQuickAccessLink(page);
      expect(textColumn).to.be.contains(boQuickAccessPage.successfulDeleteMessage);
    });

    it('should reset the filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boQuickAccessPage.resetFilter(page);

      const numberEnabledCurrencies = await boQuickAccessPage.getNumberOfElementInGrid(page);
      expect(numberEnabledCurrencies).to.be.gt(1);
    });

    it('should delete all quick access link by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAllByBulkActions', baseContext);

      const textColumn = await boQuickAccessPage.bulkDeleteQuickAccessLink(page);
      expect(textColumn).to.be.contains(boQuickAccessPage.successfulDeleteMessage);
    });

    it('should return to dashboard page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDashboardPage', baseContext);

      await boQuickAccessPage.goToDashboardPage(page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.eq(boDashboardPage.pageTitle);
    });
  });
});
