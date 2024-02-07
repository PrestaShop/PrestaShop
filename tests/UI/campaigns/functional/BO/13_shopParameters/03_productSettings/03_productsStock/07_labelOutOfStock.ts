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
import {foProductPage} from '@pages/FO/classic/product';
import {homePage} from '@pages/FO/classic/home';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_labelOutOfStock';

describe('BO - Shop Parameters - product Settings : Set label out-of-stock with  '
  + 'allowed/denied backorders', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({
    type: 'standard',
    quantity: 0,
    labelWhenOutOfStock: ' ',
    labelWhenInStock: ' ',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Set label out-of-stock with allowed/denied backorders', async () => {
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);

      await productsPage.selectProductType(page, productData.type);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

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

    tests.forEach((test, index: number) => {
      it(`should ${test.args.action} allow ordering of out-of-stock products`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.action}AllowOrderingOutOfStock${index}`,
          baseContext,
        );

        const result = await productSettingsPage.setAllowOrderingOutOfStockStatus(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
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

        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
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
        expect(isHomePage, 'Home page was not opened').to.eq(true);
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

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should check label out-of-stock', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkOrderingOutOfStock${test.args.action}${index}`,
          baseContext,
        );

        // Check quantity and availability label
        const lastQuantityIsVisible = await foProductPage.isAddToCartButtonEnabled(page);
        expect(lastQuantityIsVisible).to.be.equal(test.args.enable);

        const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
        expect(availabilityLabel).to.contains(test.args.labelToCheck);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foProductPage.closePage(browserContext, page, 0);

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

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await productsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await productsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await productsPage.resetFilter(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
