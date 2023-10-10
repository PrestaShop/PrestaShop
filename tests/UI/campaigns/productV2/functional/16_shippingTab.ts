// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import createProductPage from '@pages/BO/catalog/productsV2/add';
import shippingTab from '@pages/BO/catalog/productsV2/add/shippingTab';
import productsPage from '@pages/BO/catalog/productsV2';
import stocksTab from '@pages/BO/catalog/productsV2/add/stocksTab';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

// Import FO pages
import foProductPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';

// Import data
import ProductData from '@data/faker/product';
import Customers from '@data/demo/customers';
import Carriers from '@data/demo/carriers';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'productV2_functional_shippingTab';

describe('BO - Catalog - Products : Shipping tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'standard',
    quantity: 10,
    status: true,
  });
  // Data to edit standard product
  const editProductData: ProductData = new ProductData({
    quantity: -10,
    packageDimensionWidth: 12,
    packageDimensionHeight: 12,
    packageDimensionDepth: 12,
    packageDimensionWeight: 12,
    deliveryTime: 'None',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
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

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in shipping tab
  describe('Check all options in shipping tab', async () => {
    it('should go to shipping tab and edit package dimension', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension', baseContext);

      await shippingTab.setPackageDimension(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should edit delivery time to \'None\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime', baseContext);

      await shippingTab.setDeliveryTime(page, editProductData.deliveryTime);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that no delivery time is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTime', baseContext);

      const isDisplayed = await foProductPage.isDeliveryTimeDisplayed(page);
      expect(isDisplayed).to.equal(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should edit delivery time to \'Default delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime2', baseContext);

      await shippingTab.setDeliveryTime(page, 'Default delivery time');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that no delivery time is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTime1', baseContext);

      const isDisplayed = await foProductPage.isDeliveryTimeDisplayed(page);
      expect(isDisplayed).to.equal(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should click on \'Edit delivery time\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditDeliveryTimeLink', baseContext);

      page = await shippingTab.clickOnEditDeliveryTimeLink(page);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await productSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should edit delivery time to \'Specific delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime3', baseContext);

      await shippingTab.setDeliveryTime(page, 'Specific delivery time');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should set delivery time of in-stock products and out-of-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTime', baseContext);

      await shippingTab.setDeliveryTimeInStockProducts(page, '1 Day');
      await shippingTab.setDeliveryTimeOutOfStockProducts(page, '12 Days');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time in stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock', baseContext);

      const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('1 Day');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should update the product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityTo0', baseContext);

      await stocksTab.setProductStock(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should allow order when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allowOrderWhenOutOfStock', baseContext);

      await stocksTab.setOptionWhenOutOfStock(page, 'Allow orders');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time in stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock2', baseContext);

      const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('12 Days');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should set additional shipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAdditionalShippingCosts', baseContext);

      await shippingTab.setAdditionalShippingCosts(page, 10);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page, 1);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should proceed to checkout and validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, Customers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to delivery address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
    });

    it('should select the first carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await checkoutPage.chooseShippingMethod(page, Carriers.myCarrier.id);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.equal('€20.40');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should select available carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCarrier', baseContext);

      await shippingTab.selectAvailableCarrier(page, 'Click and collect');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCartPage', baseContext);

      await foProductPage.goToCartPage(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartPage.pageTitle);
    });

    it('should proceed to checkout and check the shipping methods', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingMethods', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.eq([Carriers.default.name]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO6', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await createProductPage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
