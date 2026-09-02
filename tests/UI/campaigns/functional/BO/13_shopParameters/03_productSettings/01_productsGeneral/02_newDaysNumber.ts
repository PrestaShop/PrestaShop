// Import utils
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

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_newDaysNumber';

/*
Update new days number to 0
Check that there is no new products in FO
Go back to the default value
Check that all products are new in FO
 */
describe('BO - Shop Parameters - Product Settings : Update Number of days for which '
  + 'the product is considered \'new\'', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
  });

  [
    {value: 0, exist: false, state: 'NotVisible'},
    {value: 20, exist: true, state: 'Visible'},
  ].forEach((arg: {value: number, exist: boolean, state: string}) => {
    it(`should update Number of days to ${arg.value}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateNumberOfDaysTo${arg.value}`, baseContext);

      const result = await boProductSettingsPage.updateNumberOfDays(page, arg.value);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${arg.state}`, baseContext);

      page = await boProductSettingsPage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check the new flag in the product miniature in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNewFlagIs${arg.state}`, baseContext);

      const isNewFlagVisible = await foHummingbirdHomePage.isNewFlagVisible(page, 1);
      expect(isNewFlagVisible).to.be.equal(arg.exist);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${arg.state}`, baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });
  });
});
