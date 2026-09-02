import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopUrlPage,
  boMultistoreShopUrlCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const ShopUrlData: FakerShop = new FakerShop({name: 'ToDelete', shopGroup: '', categoryRoot: ''});

  // Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 2 : Go to multistore page
  describe('Go to \'Multistore\' page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to shop Urls page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await boMultistorePage.goToShopURLPage(page, 1);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should reset filter and get the number of shop urls', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfShopUrls = await boMultistoreShopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrls).to.be.above(0);
    });
  });

  // 3 : Create shop url
  describe('Create shop Url', async () => {
    it('should go to add shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopUrlPage.goToAddNewUrl(page);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should create shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, ShopUrlData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  // 4 : Quick edit shop url
  describe('Quick edit shop url', async () => {
    it('should filter list by URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

      await boMultistoreShopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const numberOfShopUrlsAfterFilter = await boMultistoreShopUrlPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfShopUrlsAfterFilter; i++) {
        const textColumn = await boMultistoreShopUrlPage.getTextColumn(page, i, 'url');
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

        const isActionPerformed = await boMultistoreShopUrlPage.setStatus(page, 1, test.args.column, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);

          if (test.args.columnName === 'Enabled') {
            expect(resultMessage).to.contains(boMultistoreShopUrlPage.successUpdateMessage);
          } else {
            expect(resultMessage).to.contains(boMultistoreShopUrlPage.successfulUpdateMessage);
          }
        }

        const carrierStatus = await boMultistoreShopUrlPage.getStatus(page, 1, test.args.column);
        expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterEnableDisable', baseContext);

      const numberOfShopUrlsAfterReset = await boMultistoreShopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrlsAfterReset).to.be.equal(numberOfShopUrls + 1);
    });

    it('should set the default URL as the main URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDefaultMainURL', baseContext);

      const isActionPerformed = await boMultistoreShopUrlPage.setStatus(page, 1, '5', true);

      if (isActionPerformed) {
        const resultMessage = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(boMultistoreShopUrlPage.successfulUpdateMessage);
      }

      const carrierStatus = await boMultistoreShopUrlPage.getStatus(page, 1, '5');
      expect(carrierStatus).to.be.equal(true);
    });
  });

  // 5 : Bulk actions shop url
  describe('Bulk actions shop url', async () => {
    it('should filter list by URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await boMultistoreShopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const numberOfShopUrlsAfterFilter = await boMultistoreShopUrlPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfShopUrlsAfterFilter; i++) {
        const textColumn = await boMultistoreShopUrlPage.getTextColumn(page, i, 'url');
        expect(textColumn).to.contains(ShopUrlData.name);
      }
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((test: { args: { status: string, enable: boolean } }) => {
      it(`should ${test.args.status} shop url with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.status}ShopUrl`, baseContext);

        await boMultistoreShopUrlPage.bulkSetStatus(page, test.args.enable);

        const textResult = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);
        expect(textResult, 'Status is not updated!').to.contains(boMultistoreShopUrlPage.successUpdateMessage);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkActions', baseContext);

      const numberOfShopUrlsAfterReset = await boMultistoreShopUrlPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopUrlsAfterReset).to.be.equal(numberOfShopUrls + 1);
    });
  });

  // 6 : Delete created shop url
  describe('delete the created shop url', async () => {
    it('should delete the shop url contains \'ToDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShopUrl', baseContext);

      await boMultistoreShopUrlPage.filterTable(page, 'input', 'url', ShopUrlData.name);

      const textResult = await boMultistoreShopUrlPage.deleteShopURL(page, 1);
      expect(textResult).to.contains(boMultistoreShopUrlPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
