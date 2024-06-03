// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
// Import BO pages
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psNewProducts from '@pages/BO/modules/psNewProducts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_configuration_configureSettingsNumberOfDays';

describe('New products block module - Configure settings of "Number of days for which the product is considered \'new\'" field',
  async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let defaultValue: string;

    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psNewProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

      const pageTitle = await psNewProducts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psNewProducts.pageSubTitle);

      defaultValue = await psNewProducts.getNumDaysConsideredAsNew(page);
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

        const textResult = await psNewProducts.setNumDaysConsideredAsNew(page, arg.setting);
        expect(textResult).to.contains(psNewProducts.updateSettingsSuccessMessage);
      });

      it('should go to the front office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToTheFo${index}`, baseContext);

        page = await psNewProducts.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should check the block "New Products" is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBlockNewProductsVisible${index}`, baseContext);

        const hasProductsBlock = await homePage.hasProductsBlock(page, 'newproducts');
        expect(hasProductsBlock).to.be.equal(arg.blockIsVisible);
      });

      it('should return to the back office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToBO${index}`, baseContext);

        page = await homePage.closePage(browserContext, page, 0);

        const pageTitle = await psNewProducts.getPageSubtitle(page);
        expect(pageTitle).to.eq(psNewProducts.pageSubTitle);
      });
    });

    it('should reset the configuration in the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDefaultValue', baseContext);

      const textResult = await psNewProducts.setNumDaysConsideredAsNew(page, defaultValue);
      expect(textResult).to.contains(psNewProducts.updateSettingsSuccessMessage);
    });
  });
