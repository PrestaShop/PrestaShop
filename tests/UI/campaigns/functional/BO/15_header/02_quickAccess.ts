// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import statsPage from '@pages/BO/stats';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import newCategoryPage from '@pages/BO/catalog/categories/add';
import newVoucherPage from '@pages/BO/catalog/discounts/add';
import newProductPage from '@pages/BO/catalog/products/add';
import ordersPage from '@pages/BO/orders';
import quickAccessPage from '@pages/BO/quickAccess';
import addNewQuickAccessPage from '@pages/BO/quickAccess/add';
import newCustomerPage from '@pages/BO/customers/add';

// Import data
import QuickAccessData from '@data/faker/quickAccess';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_header_quickAccess';

describe('BO - Header : Quick access links', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const quickAccessLinkData: QuickAccessData = new QuickAccessData({
    name: 'New customer',
    url: 'index.php/sell/customers/new',
    openNewWindow: true,
  });

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_enableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check quick access links', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    [
      {args: {pageName: 'Catalog evaluation', idLink: 1, pageTitle: statsPage.pageTitle}},
      {args: {pageName: 'Installed modules', idLink: 2, pageTitle: moduleManagerPage.pageTitle}},
      {args: {pageName: 'New category', idLink: 3, pageTitle: newCategoryPage.pageTitleCreate}},
      {args: {pageName: 'New product', idLink: 4, pageTitle: newProductPage.pageTitle}},
      {args: {pageName: 'Orders', idLink: 6, pageTitle: ordersPage.pageTitle}},
      {args: {pageName: 'New voucher', idLink: 5, pageTitle: newVoucherPage.pageTitle}},
    ].forEach((test, index: number) => {
      it(`should check '${test.args.pageName}' link from Quick access`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkLink${index}`, baseContext);

        await dashboardPage.quickAccessToPage(page, test.args.idLink);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(test.args.pageTitle);
      });
    });

    it('should remove the last link \'New voucher\' from Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeLinkFromQuickAccess', baseContext);

      const validationMessage = await newVoucherPage.removeLinkFromQuickAccess(page);
      await expect(validationMessage).to.contains(newVoucherPage.successfulUpdateMessage);
    });

    it('should refresh the page and add current page to Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCurrentPageToQuickAccess', baseContext);

      await newVoucherPage.reloadPage(page);

      const validationMessage = await newVoucherPage.addCurrentPageToQuickAccess(page, 'New voucher');
      await expect(validationMessage).to.contains(newVoucherPage.successfulUpdateMessage);
    });

    it('should go to \'Manage quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToCreateLink', baseContext);

      await newVoucherPage.reloadPage(page);
      await newVoucherPage.goToManageQuickAccessPage(page);

      const pageTitle = await quickAccessPage.getPageTitle(page);
      await expect(pageTitle).to.contains(quickAccessPage.pageTitle);
    });

    it('should go to \'Add new quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddQuickAccessPage', baseContext);

      await quickAccessPage.goToAddNewQuickAccessPage(page);

      const pageTitle = await addNewQuickAccessPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addNewQuickAccessPage.pageTitle);
    });

    it('should create new quick access link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createQuickAccessLink', baseContext);

      const validationMessage = await addNewQuickAccessPage.setQuickAccessLink(page, quickAccessLinkData);
      await expect(validationMessage).to.contains(addNewQuickAccessPage.successfulCreationMessage);
    });

    it('should check the new link from Quick access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewLink', baseContext);

      page = await dashboardPage.quickAccessToPageNewWindow(page, 4);

      const pageTitle = await newCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newCustomerPage.pageTitleCreate);
    });

    it('should go to \'Manage quick access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToDeleteLink', baseContext);

      await newCustomerPage.goToManageQuickAccessPage(page);

      const pageTitle = await quickAccessPage.getPageTitle(page);
      await expect(pageTitle).to.contains(quickAccessPage.pageTitle);
    });

    it('should filter quick access table by link name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchByName', baseContext);

      await quickAccessPage.filterTable(page, 'input', 'name', quickAccessLinkData.name);

      const textColumn = await quickAccessPage.getTextColumn(page, 1, 'name');
      await expect(textColumn).to.contains(quickAccessLinkData.name);
    });

    it('should delete the created quick access link by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteByBulkActions', baseContext);

      const textColumn = await quickAccessPage.bulkDeleteQuickAccessLink(page);
      await expect(textColumn).to.be.contains(quickAccessPage.successfulMultiDeleteMessage);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
