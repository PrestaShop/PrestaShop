// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/products/add';
import productsPage from '@pages/BO/catalog/products';
import packTab from '@pages/BO/catalog/products/add/packTab';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import ordersPage from '@pages/BO/orders';
// Import FO pages
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_packTab';

describe('BO - Catalog - Products : Pack Tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productStockDemo1: number = 0;
  let productStockDemo9: number = 0;

  const productNameEn: string = 'My pack';
  const productNameFr: string = 'Mon pack';
  const productRetailPrice: number = 25.00;
  const productQuantity: number = 4;
  const productStock: number = 100;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Create the product', async () => {
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

    it(`should filter the product "${Products.demo_1.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo1', baseContext);

      await productsPage.resetFilter(page);
      await productsPage.filterProducts(page, 'product_name', Products.demo_1.name);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await productsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(Products.demo_1.reference);

      productStockDemo1 = await productsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStockDemo1).to.be.gte(1);
    });

    it(`should filter the product "${Products.demo_9.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo9', baseContext);

      await productsPage.resetFilter(page);
      await productsPage.filterProducts(page, 'product_name', Products.demo_9.name);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await productsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(Products.demo_9.reference);

      productStockDemo9 = await productsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStockDemo9).to.be.gte(1);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBeforeNew', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePackProduct', baseContext);

      await productsPage.selectProductType(page, 'pack');

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductNameEn', baseContext);

      await createProductsPage.setProductName(page, productNameEn, 'en');
      await createProductsPage.setProductName(page, productNameFr, 'fr');
      await createProductsPage.setProductStatus(page, true);

      const message = await createProductsPage.saveProduct(page);
      expect(message).to.eq(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('BO - Edit properties of the pack', async () => {
    it('should go to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPackTab', baseContext);

      // The Pack Tab has the identifier `stock`
      await createProductsPage.goToTab(page, 'stock');

      const isTabActive = await createProductsPage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should add a product by product name to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByNameToPack', baseContext);

      await packTab.addProductToPack(page, 'shirt', 1);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(1);

      const result = await packTab.getProductInPackInformation(page, 1);
      await Promise.all([
        expect(result.name).to.equal(
          `${Products.demo_1.name}: `
          + `${basicHelper.capitalize(Products.demo_1.attributes[0].name)} - ${Products.demo_1.attributes[0].values[0]}, `
          + `${basicHelper.capitalize(Products.demo_1.attributes[1].name)} - ${Products.demo_1.attributes[1].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${Products.demo_1.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should add a product by reference to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByRefToPack', baseContext);

      await packTab.addProductToPack(page, Products.demo_14.reference, 1);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await packTab.getProductInPackInformation(page, 2);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_14.name),
        expect(result.reference).to.equal(`Ref: ${Products.demo_14.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should add set quantity for the first product in pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantityInPack', baseContext);

      await packTab.setProductQuantity(page, 0, productQuantity);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await packTab.getProductInPackInformation(page, 1);
      await Promise.all([
        expect(result.name).to.equal(
          `${Products.demo_1.name}: `
          + `${basicHelper.capitalize(Products.demo_1.attributes[0].name)} - ${Products.demo_1.attributes[0].values[0]}, `
          + `${basicHelper.capitalize(Products.demo_1.attributes[1].name)} - ${Products.demo_1.attributes[1].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${Products.demo_1.reference}`),
        expect(result.quantity).to.equal(productQuantity),
      ]);
    });

    it('should delete the second product in pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductInPack', baseContext);

      await packTab.deleteProduct(page, 2, true);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(1);
    });

    it('should add a product by reference to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByRefToPack2', baseContext);

      await packTab.addProductToPack(page, Products.demo_9.reference, 1);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await packTab.getProductInPackInformation(page, 2);
      await Promise.all([
        expect(result.name).to.equal(
          `${Products.demo_9.name}: `
          + `${basicHelper.capitalize(Products.demo_9.attributes[0].name)} - ${Products.demo_9.attributes[0].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${Products.demo_9.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should edit quantity to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantityPack', baseContext);

      await packTab.editQuantity(page, productStock);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should go to the Pricing tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPricingTab', baseContext);

      await createProductsPage.goToTab(page, 'pricing');

      const isTabActive = await createProductsPage.isTabActive(page, 'pricing');
      expect(isTabActive).to.be.eq(true);
    });

    it('should set the retail price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setRetailPrice', baseContext);

      await pricingTab.setRetailPrice(page, true, productRetailPrice);

      const message = await createProductsPage.saveProduct(page);
      expect(message).to.eq(createProductsPage.successfulUpdateMessage);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      expect(productHeaderSummary.priceTaxExc).to.equals(`€${productRetailPrice.toFixed(2)} tax excl.`);
      expect(productHeaderSummary.priceTaxIncl).to.equals(
        `€${productRetailPrice.toFixed(2)} tax incl. (tax rule: 0%)`,
      );
    });

    it('should preview the product on front-office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPack', baseContext);

      page = await createProductsPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productNameEn);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const productInformation = await foProductPage.getProductInformation(page);
      expect(productRetailPrice).to.eq(productInformation.price);

      const productsPrice = await foProductPage.getPackProductsPrice(page);
      const calculatedPrice = (
        ((
          (Products.demo_1.price - (Products.demo_1.price * (Products.demo_1.specificPrice.discount / 100)))
          * (1 + (Products.demo_1.tax / 100))
        ) * productQuantity)
        + Products.demo_9.finalPrice
      ).toFixed(2);
      expect(calculatedPrice).to.eq(productsPrice.toString());

      const product1 = await foProductPage.getProductInPackList(page, 1);
      await Promise.all([
        expect(product1.name).to.equals(
          `${Products.demo_1.name} `
          + `${basicHelper.capitalize(Products.demo_1.attributes[0].name)}-${Products.demo_1.attributes[0].values[0]} `
          + `${basicHelper.capitalize(Products.demo_1.attributes[1].name)}-${Products.demo_1.attributes[1].values[0]}`,
        ),
        expect(product1.price).to.equals(`€${Products.demo_1.finalPrice.toFixed(2)}`),
        expect(product1.quantity).to.equals(productQuantity),
      ]);

      const product2 = await foProductPage.getProductInPackList(page, 2);
      await Promise.all([
        expect(product2.name).to.equals(
          `${Products.demo_9.name} `
          + `${basicHelper.capitalize(Products.demo_9.attributes[0].name)}-${Products.demo_9.attributes[0].values[0]}`,
        ),
        expect(product2.price).to.equals(`€${Products.demo_9.finalPrice.toFixed(2)}`),
        expect(product2.quantity).to.equals(1),
      ]);
    });
  });

  describe('Order the pack on FO & check stock', async () => {
    it('should order the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPack1', baseContext);

      // Add product to the cart
      await foProductPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);
      // Connect
      await checkoutPage.clickOnSignIn(page);
      await checkoutPage.customerLogin(page, dataCustomers.johnDoe);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.be.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.be.eq(true);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBackOffice', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);
      await page.reload();

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should return to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToPackTab', baseContext);

      // The Pack Tab has the identifier `stock`
      await createProductsPage.goToTab(page, 'stock');

      const isTabActive = await createProductsPage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should check the stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStock', baseContext);

      const stockValue = await packTab.getStockValue(page);
      expect(stockValue).to.be.equals(productStock - 1);
    });
  });

  describe(`Set the order to ${dataOrderStatuses.delivered.name} & check stock`, async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle: string = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult: string = await ordersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await productsPage.goToProductPage(page);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should return to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToPackTab1', baseContext);

      // The Pack Tab has the identifier `stock`
      await createProductsPage.goToTab(page, 'stock');

      const isTabActive = await createProductsPage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRecentStockMovement', baseContext);

      const result = await packTab.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains('Shipped products'),
        expect(result.employee).to.equal(''),
        expect(result.quantity).to.equal(-1),
      ]);
    });
  });

  describe('Decrement products in pack only - Configure, Order & Check stocks', async () => {
    it('should edit Pack Quantities "Decrement products in pack only"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackQuantities', baseContext);

      await packTab.editPackStockType(page, 'Decrement products in pack only');

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview the product on front-office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPack2', baseContext);

      page = await createProductsPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productNameEn);
    });

    it('should order the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPack2', baseContext);

      // Add product to the cart
      await foProductPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.be.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.be.eq(true);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBackOffice1', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProductsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it(`should filter and check the product "${Products.demo_1.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo11', baseContext);

      await productsPage.resetFilter(page);
      await productsPage.filterProducts(page, 'product_name', Products.demo_1.name);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await productsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(Products.demo_1.reference);

      const productStock = await productsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStock).to.be.gte(1);

      expect(productStock).to.be.eq(productStockDemo1 - productQuantity);
    });

    it(`should filter and check the product "${Products.demo_9.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo91', baseContext);

      await productsPage.resetFilter(page);
      await productsPage.filterProducts(page, 'product_name', Products.demo_9.name);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await productsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(Products.demo_9.reference);

      const productStock = await productsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStock).to.be.gte(1);

      expect(productStock).to.be.eq(productStockDemo9 - 1);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  deleteProductTest(new ProductData({
    name: productNameEn,
  }), `${baseContext}_postTest_0`);
});
