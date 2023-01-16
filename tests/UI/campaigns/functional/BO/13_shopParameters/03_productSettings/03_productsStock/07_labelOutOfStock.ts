// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import productPage from '@pages/FO/product';
import homePage from '@pages/FO/home';
import searchResultsPage from '@pages/FO/searchResults';

// Import data
import ProductFaker from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_labelOutOfStock';

describe('BO - Shop Parameters - product Settings : Set label out-of-stock with  '
  + 'allowed/denied backorders', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductFaker = new ProductFaker({type: 'Standard product', quantity: 0});

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

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );
    await productsPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);

    const validationMessage = await addProductPage.createEditBasicProduct(page, productData);
    await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await addProductPage.goToSubMenu(
      page,
      addProductPage.shopParametersParentLink,
      addProductPage.productSettingsLink,
    );

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {
      args: {
        action: 'enable',
        enable: true,
        backordersAction: 'allowed',
        label: 'You can order',
        labelToCheck: 'You can order',
      },
    },
    {
      args: {
        action: 'enable', enable: true, backordersAction: 'allowed', label: ' ', labelToCheck: '',
      },
    },
    {
      args: {
        action: 'disable', enable: false, backordersAction: 'denied', label: ' ', labelToCheck: '',
      },
    },
    {
      args: {
        action: 'disable',
        enable: false,
        backordersAction: 'denied',
        label: 'Out-of-Stock',
        labelToCheck: 'Out-of-Stock',
      },
    },
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} allow ordering of out-of-stock products`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}AllowOrderingOutOfStock${index}`,
        baseContext,
      );

      const result = await productSettingsPage.setAllowOrderingOutOfStockStatus(page, test.args.enable);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it(`should set Label of out-of-stock products with ${test.args.backordersAction} backorders`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `setLabelOutOfStock${index}`,
        baseContext,
      );

      let result;

      if (test.args.enable) {
        result = await productSettingsPage.setLabelOosAllowedBackorders(page, test.args.label);
      } else {
        result = await productSettingsPage.setLabelOosDeniedBackorders(page, test.args.label);
      }

      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `viewMyShop${test.args.action}${index}`,
        baseContext,
      );

      page = await productSettingsPage.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page was not opened').to.be.true;
    });

    it('should search for the product and go to product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToProductPage${test.args.action}${index}`,
        baseContext,
      );

      // Search and go to product page
      await homePage.searchProduct(page, productData.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productData.name);
    });

    it('should check label out-of-stock', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkOrderingOutOfStock${test.args.action}${index}`,
        baseContext,
      );

      // Check quantity and availability label
      const lastQuantityIsVisible = await productPage.isAddToCartButtonEnabled(page);
      await expect(lastQuantityIsVisible).to.be.equal(test.args.enable);

      const availabilityLabel = await productPage.getProductAvailabilityLabel(page);
      await expect(availabilityLabel).to.contains(test.args.labelToCheck);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

    await productSettingsPage.goToSubMenu(
      page,
      productSettingsPage.catalogParentLink,
      productSettingsPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const deleteTextResult = await productsPage.deleteProduct(page, productData);
    await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    await productsPage.resetFilterCategory(page);

    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });
});
