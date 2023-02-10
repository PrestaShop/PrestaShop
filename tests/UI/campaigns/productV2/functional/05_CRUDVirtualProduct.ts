import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import utils
import helper from '@utils/helpers';
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import productsPage from '@pages/BO/catalog/productsV2';
import ordersPage from '@pages/BO/orders';
import {homePage} from '@pages/FO/home';
import foProductPage from '@pages/FO/product';
import cartPage from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {myAccountPage} from '@pages/FO/myAccount';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {
  enableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import ProductData from '@data/faker/product';

const baseContext: string = 'productV2_functional_CRUDVirtualProduct';

describe('BO - Catalog - Products : CRUD virtual product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'virtual',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    downloadFile: true,
    fileName: 'virtual.jpg',
    allowedDownload: 1,
    price: 15,
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });
  const editProductData: ProductData = new ProductData({
    type: 'virtual',
    taxRule: 'FR Taux réduit (10%)',
    tax: 10,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // Pre-condition: Enable new product page
  enableNewProductPageTest(`${baseContext}_enableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.generateImage(newProductData.thumbImage);
    }
    await files.generateImage(newProductData.fileName);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.deleteFile(newProductData.thumbImage);
    }
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

      const pageTitle: string = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible: boolean = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Virtual product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should check the virtual product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDescription', baseContext);

      const productTypeDescription: string = await productsPage.getProductDescription(page);
      await expect(productTypeDescription).to.contains(productsPage.virtualProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewfoProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage: string = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  // 2 - View product
  describe('View product in BO & FO', async () => {
    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue: number = await basicHelper.percentage(newProductData.price, newProductData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(newProductData.price.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(newProductData.price + taxValue).toFixed(2)} tax incl. (tax rule: ${newProductData.tax}%)`),
        expect(productHeaderSummary.quantity).to.equal(`${newProductData.quantity} in stock`),
        expect(productHeaderSummary.reference).to.contains(newProductData.reference),
      ]);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName: string = await createProductsPage.getSaveButtonName(page);
      await expect(saveButtonName).to.equal(createProductsPage.saveAndPublishButtonName);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue: number = await basicHelper.percentage(newProductData.price, newProductData.tax);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(newProductData.name),
        await expect(result.price.toFixed(2)).to.equal((newProductData.price + taxValue).toFixed(2)),
        await expect(result.summary).to.equal(newProductData.summary),
        await expect(result.description).to.equal(newProductData.description),
      ]);
    });

    describe('Create order to check uploaded file', async () => {
      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the product to the cart
        await foProductPage.addProductToTheCart(page, 1);

        const notificationsNumber: number = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(1);
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

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should pay the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle: string = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

        // Go back to BO
        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await createProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle: string = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should update order status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const textResult: string = await ordersPage.setOrderStatus(page, 1, OrderStatuses.paymentAccepted);
        await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

        // Click on view my shop
        page = await ordersPage.viewMyShop(page);

        const isHomePage: boolean = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to order history page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await myAccountPage.goToHistoryAndDetailsPage(page);

        const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
        await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
      });

      it('should go to order details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

        await foOrderHistoryPage.goToDetailsPage(page);

        const pageTitle: string = await orderDetailsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
      });

      it('should download the file', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

        await orderDetailsPage.clickOnDownloadLink(page);

        const doesFileExist: boolean = await files.doesFileExist(newProductData.fileName, 5000);
        await expect(doesFileExist, 'File is not downloaded!').to.be.true;
      });
    });
  });

  // 3 - Edit product
  describe('Edit product', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle: string = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await productsPage.goToProductPage(page);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedProduct', baseContext);

      const createProductMessage: string = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue: number = await basicHelper.percentage(editProductData.price, editProductData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(editProductData.price.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(editProductData.price + taxValue).toFixed(2)} tax incl. (tax rule: ${editProductData.tax}%)`),
        expect(productHeaderSummary.quantity).to.equal(`${editProductData.quantity + newProductData.quantity - 1} in stock`),
        expect(productHeaderSummary.reference).to.contains(editProductData.reference),
      ]);
    });
  });

  // 4 - View edited product
  describe('View edited product', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const taxValue: number = await basicHelper.percentage(editProductData.price, editProductData.tax);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price.toFixed(2)).to.equal((editProductData.price + taxValue).toFixed(2)),
        await expect(result.description).to.equal(editProductData.description),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  // 5 - Delete product
  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage: string = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
