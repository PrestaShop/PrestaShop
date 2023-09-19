// Import utils
import helper from '@utils/helpers';
import files from '@utils/files';
import testContext from '@utils/testContext';
import date from '@utils/date';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import productsPage from '@pages/BO/catalog/productsV2';
import foProductPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {homePage} from '@pages/FO/home';
import {myAccountPage} from '@pages/FO/myAccount';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';
import virtualProductTab from '@pages/BO/catalog/productsV2/add/virtualProductTab';
import productSettings from '@pages/BO/shopParameters/productSettings';

// Import data
import ProductData from '@data/faker/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'productV2_functional_virtualTab';

describe('BO - Catalog - Products : Virtual tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'virtual',
    price: 0,
    quantity: 100,
    minimumQuantity: 1,
    downloadFile: true,
    fileName: 'virtual.jpg',
    allowedDownload: 0,
    expirationDate: futureDate,
    numberOfDays: 0,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage(newProductData.fileName);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(newProductData.fileName);
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Virtual product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct', baseContext);

      const createProductMessage: string = await createProductsPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  // 2 - Create order and download the virtual product
  describe('Create order and download the virtual product', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Add the product to the cart
      await foProductPage.addProductToTheCart(page, 1);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(1);
    });

    it('should proceed to checkout and sign in', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Personal information step - Login
      await checkoutPage.clickOnSignIn(page);
      await checkoutPage.customerLogin(page, Customers.johnDoe);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should pay the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should download the file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

      await orderDetailsPage.clickOnDownloadLink(page);

      const doesFileExist = await files.doesFileExist(newProductData.fileName, 5000);
      await expect(doesFileExist, 'File is not downloaded!').to.be.true;
    });

    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  // 3 - Check the options of out of stock
  describe('Check the options of out of stock', async () => {
    it('should set the product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductQuantity', baseContext);

      await virtualProductTab.setProductQuantity(page, -100);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    [
      {args: {option: 'Deny orders', isAddToCartButtonVisible: false}},
      {args: {option: 'Allow orders', isAddToCartButtonVisible: true}},
      {args: {option: 'Use default behavior', isAddToCartButtonVisible: false}},
    ].forEach((test, index) => {
      it(`should check the '${test.args.option}' option`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderOption${index}`, baseContext);

        await virtualProductTab.setOptionWhenOutOfStock(page, test.args.option);

        const createProductMessage = await createProductsPage.saveProduct(page);
        expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });

      it('should preview product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `previewProduct${index}`, baseContext);

        // Click on preview button
        page = await createProductsPage.previewProduct(page);

        await foProductPage.changeLanguage(page, 'en');

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(newProductData.name);
      });

      it('should check the add to cart button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton${index}`, baseContext);

        const isVisible = await foProductPage.isAddToCartButtonEnabled(page);
        expect(isVisible).to.eq(test.args.isAddToCartButtonVisible);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        // Go back to BO
        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await createProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });
    });

    it('should click on edit default behaviour link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editBehaviour', baseContext);

      page = await virtualProductTab.clickOnEditDefaultBehaviourLink(page);

      const pageTitle = await productSettings.getPageTitle(page);
      expect(pageTitle).to.contains(productSettings.pageTitle);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await productSettings.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should set label when in stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setLabelWhenInStock', baseContext);

      await virtualProductTab.setProductQuantity(page, 100);
      await virtualProductTab.setLabelWhenInStock(page, 'Product available');

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product availability label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAvailableLabel', baseContext);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      await expect(availabilityLabel).to.contains('Product available');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should check the allow orders option and set Label when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDenyOrder', baseContext);

      await virtualProductTab.setProductQuantity(page, -100);
      await virtualProductTab.setOptionWhenOutOfStock(page, 'Allow orders');

      await virtualProductTab.setLabelWhenOutOfStock(page, 'Out of stock');

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the label of out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLabelOutOfStock', baseContext);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      await expect(availabilityLabel).to.contains('Out of stock');

      const isVisible = await foProductPage.isAddToCartButtonEnabled(page);
      await expect(isVisible).to.be.true;
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  // 4 - Delete product
  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage: string = await createProductsPage.deleteProduct(page);
      expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
