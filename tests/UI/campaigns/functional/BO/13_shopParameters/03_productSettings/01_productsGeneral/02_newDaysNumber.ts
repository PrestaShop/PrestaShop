// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_newDaysNumber';

/*
Update new days number to 0
Check that there is no new products in FO
Go back to the default value
Check that all products are new in FO
 */
describe('BO - Shop Parameters - Product Settings : Update Number of days for which  '
  + 'the product is considered \'new\'', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );
    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {value: 0, exist: false, state: 'NotVisible'}},
    {args: {value: 20, exist: true, state: 'Visible'}},
  ];

  tests.forEach((test) => {
    it(`should update Number of days to ${test.args.value}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateNumberOfDaysTo${test.args.value}`, baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, test.args.value);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${test.args.state}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check the new flag in the product miniature in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNewFlagIs${test.args.state}`, baseContext);

      const isNewFlagVisible = await homePage.isNewFlagVisible(page, 1);
      expect(isNewFlagVisible).to.be.equal(test.args.exist);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${test.args.state}`, baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
