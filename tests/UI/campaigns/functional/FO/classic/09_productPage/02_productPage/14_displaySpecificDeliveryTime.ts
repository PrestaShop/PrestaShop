// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import productsPage from '@pages/BO/catalog/products';
import createProductPage from '@pages/BO/catalog/products/add';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import shippingTab from '@pages/BO/catalog/products/add/shippingTab';

// Import FO pages
import {productPage as foProductPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerProduct,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displaySpecificDeliveryTime';

describe('FO - Product page - Product page : Display specific delivery time', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create new product
  const newProductData: FakerProduct = new FakerProduct({
    name: 'test',
    type: 'standard',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create standard product', async () => {
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

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePackOfProducts', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await createProductPage.closeSfToolBar(page);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should choose the option \'Allow orders\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAllowOrders', baseContext);

      await createProductPage.goToTab(page, 'stock');
      await stocksTab.setOptionWhenOutOfStock(page, 'Allow orders');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should choose the option \'Specific delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSpecificDeliveryTime', baseContext);

      await createProductPage.goToTab(page, 'shipping');
      await shippingTab.setDeliveryTime(page, 'Specific delivery time');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should set delivery time out-of-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTimeOutOfStock', baseContext);

      await shippingTab.setDeliveryTimeOutOfStockProducts(page, 'Hello');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  describe('Check Specific delivery time on Product page', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time out of stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeOutOfStock', baseContext);

      const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('Hello');
    });
  });

  describe('Edit specific delivery time of the created product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should edit the product quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity', baseContext);

      await createProductPage.goToTab(page, 'stock');
      await stocksTab.setProductQuantity(page, 100);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should set delivery time of in-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTime2', baseContext);

      await createProductPage.goToTab(page, 'shipping');
      await shippingTab.setDeliveryTimeInStockProducts(page, 'Delivered in less than a week');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  describe('Check Specific delivery time on Product page', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time out of stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock2', baseContext);

      const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('Delivered in less than a week');
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
