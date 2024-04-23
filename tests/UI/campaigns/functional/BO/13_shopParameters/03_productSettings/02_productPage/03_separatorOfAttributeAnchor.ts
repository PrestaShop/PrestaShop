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
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_separatorOfAttributeAnchor';

describe('BO - Shop Parameters - Product Settings : Update separator of attribute anchor on '
  + 'the product links', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productAttributes: string[] = ['1', 'size', 's/8', 'color', 'white'];

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

    const pageTitle = await productSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {option: ',', attributesInProductLink: productAttributes.join(',')}},
    {args: {option: '-', attributesInProductLink: productAttributes.join('-')}},
  ];

  tests.forEach((test, index: number) => {
    it(`should choose the separator option '${test.args.option}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `chooseOption_${index}`, baseContext);

      const result = await productSettingsPage.setSeparatorOfAttributeOnProductLink(
        page,
        test.args.option,
      );
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page was not opened').to.eq(true);
    });

    it('should search for the product and go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

      await homePage.searchProduct(page, Products.demo_1.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the attribute separator on the product links in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAttributeSeparator_${index}`, baseContext);

      const currentURL = await productPage.getProductPageURL(page);
      expect(currentURL).to.contains(test.args.attributesInProductLink);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
