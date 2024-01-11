// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';
import shopUrlPage from '@pages/BO/advancedParameters/multistore/url';

// Import data
import ShopData from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_quickEditAndBulkActionsShopUrls';

/*
Enable multistore
Create shop url
Quick edit (enable, disable)
Bulk actions (enable, disable)
Deleted created shop url
Disable multistore
 */
describe('BO - Advanced Parameters - Multistore : Quick edit and bulk actions shop Urls', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShopUrls: number = 0;
  const ShopUrlData: ShopData = new ShopData({name: 'ToDelete', shopGroup: '', categoryRoot: ''});

  // Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 2 : Go to multistore page
  describe('Go to \'Multistore\' page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to shop Urls page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await multiStorePage.goToShopURLPage(page, 1);

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should reset filter and get the number of shop urls', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfShopUrls = await shopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrls).to.be.above(0);
    });
  });

  // 3 : Create shop url
  describe('Create shop Url', async () => {
    it('should go to add shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopUrlPage.goToAddNewUrl(page);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should create shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, ShopUrlData.name);
      expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });
  });

  // 4 : Quick edit shop url
  describe('Quick edit shop url', async () => {
    it('should filter list by URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

      await shopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const numberOfShopUrlsAfterFilter = await shopUrlPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfShopUrlsAfterFilter; i++) {
        const textColumn = await shopUrlPage.getTextColumn(page, i, 'url');
        expect(textColumn).to.contains(ShopUrlData.name);
      }
    });

    [
      {
        args: {
          column: '6', columnName: 'Enabled', action: 'disable', enabledValue: false,
        },
      },
      {
        args: {
          column: '6', columnName: 'Enabled', action: 'enable', enabledValue: true,
        },
      },
      {
        args: {
          column: '5', columnName: 'Is it the mail URL', action: 'enable', enabledValue: true,
        },
      },
    ].forEach((test: { args: { column: string, columnName: string, action: string, enabledValue: boolean } }, index: number) => {
      it(`should ${test.args.action} the column '${test.args.columnName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}_${index}`, baseContext);

        const isActionPerformed = await shopUrlPage.setStatus(page, 1, test.args.column, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);

          if (test.args.columnName === 'Enabled') {
            expect(resultMessage).to.contains(shopUrlPage.successUpdateMessage);
          } else {
            expect(resultMessage).to.contains(shopUrlPage.successfulUpdateMessage);
          }
        }

        const carrierStatus = await shopUrlPage.getStatus(page, 1, test.args.column);
        expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterEnableDisable', baseContext);

      const numberOfShopUrlsAfterReset = await shopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrlsAfterReset).to.be.equal(numberOfShopUrls + 1);
    });

    it('should set the default URL as the main URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDefaultMainURL', baseContext);

      const isActionPerformed = await shopUrlPage.setStatus(page, 1, '5', true);

      if (isActionPerformed) {
        const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(shopUrlPage.successfulUpdateMessage);
      }

      const carrierStatus = await shopUrlPage.getStatus(page, 1, '5');
      expect(carrierStatus).to.be.equal(true);
    });
  });

  // 5 : Bulk actions shop url
  describe('Bulk actions shop url', async () => {
    it('should filter list by URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await shopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const numberOfShopUrlsAfterFilter = await shopUrlPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfShopUrlsAfterFilter; i++) {
        const textColumn = await shopUrlPage.getTextColumn(page, i, 'url');
        expect(textColumn).to.contains(ShopUrlData.name);
      }
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((test: { args: { status: string, enable: boolean } }) => {
      it(`should ${test.args.status} shop url with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.status}ShopUrl`, baseContext);

        await shopUrlPage.bulkSetStatus(page, test.args.enable);

        const textResult = await shopUrlPage.getAlertSuccessBlockContent(page);
        expect(textResult, 'Status is not updated!').to.contains(shopUrlPage.successUpdateMessage);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkActions', baseContext);

      const numberOfShopUrlsAfterReset = await shopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrlsAfterReset).to.be.equal(numberOfShopUrls + 1);
    });
  });

  // 6 : Delete created shop url
  describe('delete the created shop url', async () => {
    it('should delete the shop url contains \'ToDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShopUrl', baseContext);

      await shopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const textResult = await shopUrlPage.deleteShopURL(page, 1);
      expect(textResult).to.contains(shopUrlPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
