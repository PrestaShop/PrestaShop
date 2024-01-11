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
import foProductPage from '@pages/FO/product';
import {homePage as foHomePage} from '@pages/FO/home';
import {searchResultsPage} from '@pages/FO/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_enableDeliveryTimeOfOutOfStockProducts';

describe('BO - Shop Parameters - Product Settings : Enable delivery time out-of-stocks products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({type: 'standard', quantity: 0});

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

  describe('Create a product', async () => {
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

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });
  });

  describe('Enable delivery time out-of-stock', () => {
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
      {args: {action: 'enable', enable: true, deliveryTimeText: '8-9 days'}},
      {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
    ];
    tests.forEach((test, index: number) => {
      describe(`Check delivery time of out-of-stock products ${test.args.enable} status`, async () => {
        it(`should ${test.args.action} delivery time of out-of-stock products in BO`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

          await productSettingsPage.setAllowOrderingOutOfStockStatus(page, test.args.enable);

          const result = await productSettingsPage.setDeliveryTimeOutOfStock(
            page,
            test.args.deliveryTimeText,
          );
          expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await productSettingsPage.viewMyShop(page);

          await foHomePage.changeLanguage(page, 'en');

          const isFoHomePage = await foHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should check delivery time block visibility', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);

          await foHomePage.searchProduct(page, productData.name);
          await searchResultsPage.goToProductPage(page, 1);

          const isDeliveryTimeBlockVisible = await foProductPage.isDeliveryInformationVisible(page);
          expect(isDeliveryTimeBlockVisible).to.equal(test.args.enable);
        });

        if (test.args.enable) {
          it('should check delivery time text', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockText${index}`, baseContext);

            const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
            expect(deliveryTimeText).to.equal(test.args.deliveryTimeText);
          });
        }

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await productSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });
      });
    });
  });

  describe('Delete the product created for test ', async () => {
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
