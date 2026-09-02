// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayAvailableQuantities';

/**
 * Disable display available quantities on product page
 * Check that quantity is not displayed
 * Enable display available quantities on product page
 * Check that quantity is displayed
 */
describe('BO - Shop Parameters - Product Settings : Display available quantities on the product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    {action: 'disable', enable: false},
    {action: 'enable', enable: true},
  ].forEach((test: {action: string, enable: boolean}, index: number) => {
    it(`should ${test.action} Display available quantities on the product page`, async function () {
      await testContext.addContextItem(this,
        'testIdentifier',
        `${test.action}DisplayAvailableQuantities`,
        baseContext,
      );

      const result = await boProductSettingsPage.setDisplayAvailableQuantitiesStatus(page, test.enable);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await boProductSettingsPage.viewMyShop(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page was not opened').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should check the product quantity on the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkQuantity${index}`, baseContext);

      const quantityIsVisible = await foHummingbirdProductPage.isQuantityDisplayed(page);
      expect(quantityIsVisible).to.be.equal(test.enable);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

      page = await foHummingbirdProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });
  });
});
