// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import shopUrlPage from '@pages/BO/advancedParameters/multistore/url';
import shopPage from '@pages/BO/advancedParameters/multistore/shop';
import editShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';

// Import data
import ShopData from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_editUrlOptions';

/*
Pre-condition:
- Enable multistore
Scenario:
- Disable option "Is it the main URL for this shop?" and Disable the shop url
- Create new shop URL
- Enable option "Is it the main URL for this shop?" and enable the shop url
Post-condition:
-Disable multistore
 */
describe('BO - Advanced Parameters - Multistore : Edit URL options', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const ShopUrlData: ShopData = new ShopData({name: 'polpol', shopGroup: '', categoryRoot: ''});

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 : Go to multistore page
  describe('Go to \'Multistore\' page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to \'Shop Urls\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await multiStorePage.goToShopURLPage(page, 1);

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });
  });

  // 2 : Edit url options
  describe('Edit URL options', async () => {
    it('should go to edit the first shop URL page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopUrlsPage', baseContext);

      await shopUrlPage.goToEditShopURLPage(page, 1);

      const pageTitle = await editShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(editShopUrlPage.pageTitleEdit);
    });

    it('should disable the main URL and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainURL', baseContext);

      const errorAlertMessage = await editShopUrlPage.setMainURL(page, 'off');
      expect(errorAlertMessage).to.contains(editShopUrlPage.errorDisableMainURLMessage);
    });

    it('should disable the shop and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

      const errorAlertMessage = await editShopUrlPage.setShopStatus(page, 'off');
      expect(errorAlertMessage).to.contains(editShopUrlPage.errorDisableMainURLMessage)
        .and.to.contains(editShopUrlPage.ErrorDisableShopMessage);
    });

    it('should go to add shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopUrlPage.goToAddNewUrl(page);

      const pageTitle = await editShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(editShopUrlPage.pageTitleCreate);
    });

    it('should create shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await editShopUrlPage.setVirtualUrl(page, ShopUrlData);
      expect(textResult).to.contains(editShopUrlPage.successfulCreationMessage);
    });

    it('should disable the shop URL for the created url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShopURL', baseContext);

      await shopUrlPage.setStatus(page, 2, '6', false);

      const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);
      expect(resultMessage).to.contains(shopUrlPage.successUpdateMessage);
    });

    it('should enable the main URL for the created url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMainURL', baseContext);

      const isActionPerformed = await shopUrlPage.setStatus(page, 2, '5', true);

      if (isActionPerformed) {
        const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(shopUrlPage.successfulUpdateMessage);
      }

      const status = await shopUrlPage.getStatus(page, 1, '6');
      expect(status).to.eq(true);
    });

    it('should enable the main URL for the first url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSHopURL', baseContext);

      await shopUrlPage.setStatus(page, 1, '5', true);

      const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);
      expect(resultMessage).to.contains(shopUrlPage.successfulUpdateMessage);

      const mainStatus = await shopUrlPage.getStatus(page, 1, '6');
      expect(mainStatus).to.eq(true);
    });
  });

  // Post-condition : Delete created shop URL
  describe('POST-TEST: Delete created shop URL', async () => {
    it('should delete the created shop url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      const textResult = await shopUrlPage.deleteShopURL(page, 2);
      expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
