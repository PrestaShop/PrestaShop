// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import addProductPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boProductsPage,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_allowOrderingOutOfStock';

describe('BO - Shop Parameters - Product Settings : Allow ordering of out-of-stock products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({type: 'standard', quantity: 0});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Allow ordering of out-of-stock products', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);

      await boProductsPage.selectProductType(page, productData.type);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.shopParametersParentLink,
        addProductPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    const tests = [
      {args: {action: 'enable', enable: true}},
      {args: {action: 'disable', enable: false}},
    ];
    tests.forEach((test) => {
      it(`should ${test.args.action} allow ordering of out-of-stock products`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.action}AllowOrderingOutOfStock`,
          baseContext,
        );

        const result = await productSettingsPage.setAllowOrderingOutOfStockStatus(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${test.args.action}`, baseContext);

        // Go to FO
        page = await productSettingsPage.viewMyShop(page);

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the product and go to product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${test.args.action}`, baseContext);

        // Search and go to product page
        await foClassicHomePage.searchProduct(page, productData.name);
        await foClassicSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should check add to cart button is enabled or not', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkOrderingOutOfStock${test.args.action}`,
          baseContext,
        );

        // Check add to cart button
        const lastQuantityIsVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
        expect(lastQuantityIsVisible).to.be.equal(test.args.enable);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.args.action}`, baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await boProductsPage.resetFilter(page);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
