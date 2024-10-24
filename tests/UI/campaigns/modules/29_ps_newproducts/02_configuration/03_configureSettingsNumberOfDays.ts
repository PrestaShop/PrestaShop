// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  foClassicHomePage,
  modPsNewProductsBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_configuration_configureSettingsNumberOfDays';

describe('New products block module - Configure settings of "Number of days for which the product is considered \'new\'" field',
  async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let defaultValue: string;

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

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psNewProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

      const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);

      defaultValue = await modPsNewProductsBoMain.getNumDaysConsideredAsNew(page);
    });

    [
      {
        setting: 3,
        blockIsVisible: true,
      },
      {
        setting: -1,
        blockIsVisible: false,
      },
      {
        setting: 1000000000,
        blockIsVisible: false,
      },
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35796
      /*
      {
        setting: '1 500',
        blockIsVisible: false,
      },
      */
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35796
      /*
      {
        setting: 0,
        blockIsVisible: false,
      },
      */
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35796
      /*
      {
        setting: '@',
        blockIsVisible: false,
      },
      */
    ].forEach((arg: { setting: number|string, blockIsVisible: boolean }, index: number) => {
      it(`should change the configuration (${arg.setting}) in the module`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `changeConfiguration${index}`, baseContext);

        const textResult = await modPsNewProductsBoMain.setNumDaysConsideredAsNew(page, arg.setting);
        expect(textResult).to.contains(modPsNewProductsBoMain.updateSettingsSuccessMessage);
      });

      it('should go to the front office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToTheFo${index}`, baseContext);

        page = await modPsNewProductsBoMain.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should check the block "New Products" is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBlockNewProductsVisible${index}`, baseContext);

        const hasProductsBlock = await foClassicHomePage.hasProductsBlock(page, 'newproducts');
        expect(hasProductsBlock).to.be.equal(arg.blockIsVisible);
      });

      it('should return to the back office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToBO${index}`, baseContext);

        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
        expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
      });
    });

    it('should reset the configuration in the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDefaultValue', baseContext);

      const textResult = await modPsNewProductsBoMain.setNumDaysConsideredAsNew(page, defaultValue);
      expect(textResult).to.contains(modPsNewProductsBoMain.updateSettingsSuccessMessage);
    });
  });
