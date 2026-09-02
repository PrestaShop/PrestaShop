import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_configuration_configureSettingsNumberOfDays';

describe('New products block module - Configure settings of "Number of days for which the product is considered \'new\'" field',
  async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let defaultValue: number;

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

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.productSettingsLink,
      );
      await boProductSettingsPage.closeSfToolBar(page);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);

      defaultValue = parseInt(await boProductSettingsPage.getValue(page, 'PS_NB_DAYS_NEW_PRODUCT'), 10);
    });

    [
      {setting: 3, blockIsVisible: true},
      {setting: 0, blockIsVisible: false},
    ].forEach((arg: {setting: number, blockIsVisible: boolean}, index: number) => {
      it(`should update Number of days to ${arg.setting}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateNumberOfDaysTo${arg.setting}${index}`, baseContext);

        const result = await boProductSettingsPage.updateNumberOfDays(page, arg.setting);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await boProductSettingsPage.viewMyShop(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should check the block "New Products" is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBlockNewProductsVisible${index}`, baseContext);

        const hasProductsBlock = await foHummingbirdHomePage.hasProductsBlock(page, 'ps-newproducts');
        expect(hasProductsBlock).to.be.equal(arg.blockIsVisible);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });

    it('should reset the configuration in the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDefaultValue', baseContext);

      const textResult = await boProductSettingsPage.updateNumberOfDays(page, defaultValue);
      expect(textResult).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });
