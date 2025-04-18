import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopUrlPage,
  boMultistoreShopUrlCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const ShopUrlData: FakerShop = new FakerShop({name: 'polpol', shopGroup: '', categoryRoot: ''});

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 : Go to multistore page
  describe('Go to \'Multistore\' page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to \'Shop Urls\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await boMultistorePage.goToShopURLPage(page, 1);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });
  });

  // 2 : Edit url options
  describe('Edit URL options', async () => {
    it('should go to edit the first shop URL page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopUrlsPage', baseContext);

      await boMultistoreShopUrlPage.goToEditShopURLPage(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleEdit);
    });

    it('should disable the main URL and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainURL', baseContext);

      const errorAlertMessage = await boMultistoreShopUrlCreatePage.setMainURL(page, 'off');
      expect(errorAlertMessage).to.contains(boMultistoreShopUrlCreatePage.errorDisableMainURLMessage);
    });

    it('should disable the shop and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

      const errorAlertMessage = await boMultistoreShopUrlCreatePage.setShopStatus(page, 'off');
      expect(errorAlertMessage).to.contains(boMultistoreShopUrlCreatePage.errorDisableMainURLMessage)
        .and.to.contains(boMultistoreShopUrlCreatePage.errorDisableShopMessage);
    });

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

    it('should disable the shop URL for the created url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShopURL', baseContext);

      await boMultistoreShopUrlPage.setStatus(page, 2, '6', false);

      const resultMessage = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);
      expect(resultMessage).to.contains(boMultistoreShopUrlPage.successUpdateMessage);
    });

    it('should enable the main URL for the created url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMainURL', baseContext);

      const isActionPerformed = await boMultistoreShopUrlPage.setStatus(page, 2, '5', true);

      if (isActionPerformed) {
        const resultMessage = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(boMultistoreShopUrlPage.successfulUpdateMessage);
      }

      const status = await boMultistoreShopUrlPage.getStatus(page, 1, '6');
      expect(status).to.eq(true);
    });

    it('should enable the main URL for the first url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSHopURL', baseContext);

      await boMultistoreShopUrlPage.setStatus(page, 1, '5', true);

      const resultMessage = await boMultistoreShopUrlPage.getAlertSuccessBlockContent(page);
      expect(resultMessage).to.contains(boMultistoreShopUrlPage.successfulUpdateMessage);

      const mainStatus = await boMultistoreShopUrlPage.getStatus(page, 1, '6');
      expect(mainStatus).to.eq(true);
    });
  });

  // Post-condition : Delete created shop URL
  describe('POST-TEST: Delete created shop URL', async () => {
    it('should delete the created shop url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      const textResult = await boMultistoreShopUrlPage.deleteShopURL(page, 2);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
