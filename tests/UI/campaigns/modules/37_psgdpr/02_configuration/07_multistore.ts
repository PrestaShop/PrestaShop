import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  boShopParametersPage,
  type BrowserContext,
  dataModules,
  FakerShop,
  foClassicContactUsPage,
  foClassicHomePage,
  modPsGdprBoMain,
  modPsGdprBoTabDataConsent,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_psgdpr_configuration_multistore';

describe('BO - Modules - GDPR : Multistore', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createShopData1: FakerShop = new FakerShop({
    shopGroup: 'Default',
    categoryRoot: 'Home',
  });
  const createShopData2: FakerShop = new FakerShop({
    shopGroup: 'Default',
    categoryRoot: 'Home',
  });

  describe('Multistore', async () => {
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

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Consent checkbox customization"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabDataContent', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 3);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should disable consent message on Contact Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormStatusFalse', baseContext);

      await modPsGdprBoTabDataConsent.setContactFormStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // View my shop and get the new tab
      page = await modPsGdprBoTabDataConsent.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/37116
    it('should check on Contact Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContactFormGDPRLabel', baseContext);

      await foClassicHomePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);

      this.skip();

      const hasGDPRLabel = await foClassicContactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      page = await foClassicContactUsPage.changePage(browserContext, 0);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it('should enable multi store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMultiStore', baseContext);

      const result = await boShopParametersPage.setMultiStoreStatus(page, true);
      expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
    });

    [
      createShopData1,
      createShopData2,
    ].forEach((shop: FakerShop, index: number) => {
      it('should go to \'Advanced Parameters > Multistore\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMultiStorePage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.multistoreLink,
        );
        await boMultistorePage.closeSfToolBar(page);

        const pageTitle = await boMultistorePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistorePage.pageTitle);
      });

      it('should go to add new shop page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopPage${index}`, baseContext);

        await boMultistorePage.goToNewShopPage(page);

        const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
      });

      it('should create the shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShop${index}`, baseContext);

        const textResult = await boMultistoreShopCreatePage.setShop(page, shop);
        expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
      });

      it('should get the id of the new shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `getShopID${index}`, baseContext);

        const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
        expect(numberOfShops).to.be.above(0);

        const shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
        shop.setID(shopID);
      });

      it('should go to add URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddURL${index}`, baseContext);

        await boMultistoreShopPage.filterTable(page, 'a!name', shop.name);
        await boMultistoreShopPage.goToSetURL(page, 1);

        const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
      });

      it('should set URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addURL${index}`, baseContext);

        const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, shop.name);
        expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
      });
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageShopContext', baseContext);

      await boMultistoreShopUrlCreatePage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should click on multistore header and select the second shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectShop2', baseContext);

      await boDashboardPage.clickOnMultiStoreHeader(page);
      await boDashboardPage.chooseShop(page, 3);

      const shopName = await boDashboardPage.getShopName(page);
      expect(shopName).to.eq(createShopData2.name);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleShopContext', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/37112
    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPageShopContext', baseContext);

      this.skip();

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    // After the issue is fixed, the test will need to be completed.
  });
});
