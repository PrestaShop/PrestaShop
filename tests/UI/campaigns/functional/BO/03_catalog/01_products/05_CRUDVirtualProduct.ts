// Import utils
import helper from '@utils/helpers';
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import mailHelper from '@utils/mailHelper';
import testContext from '@utils/testContext';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/products/add';
import productsPage from '@pages/BO/catalog/products';
import ordersPage from '@pages/BO/orders';
import {homePage} from '@pages/FO/classic/home';
import {foProductPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import orderDetailsPage from '@pages/FO/classic/myAccount/orderDetails';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import ProductData from '@data/faker/product';
import type MailDevEmail from '@data/types/maildevEmail';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';
import type MailDev from 'maildev';

const baseContext: string = 'functional_BO_catalog_products_CRUDVirtualProduct';

describe('BO - Catalog - Products : CRUD virtual product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let mailListener: MailDev;

  // Data to create standard product
  const mails: MailDevEmail[] = [];
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

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

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

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      mails.push(email);
    });
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

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
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
      expect(isModalVisible).eq(true);
    });

    it('should choose \'Virtual product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should check the virtual product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDescription', baseContext);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.virtualProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFoProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  // 2 - View product
  describe('View product in BO & FO', async () => {
    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue = await basicHelper.percentage(newProductData.priceTaxExcluded, newProductData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(newProductData.priceTaxExcluded.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(newProductData.priceTaxExcluded + taxValue).toFixed(2)} tax incl. (tax rule: ${newProductData.tax}%)`),
        expect(productHeaderSummary.quantity).to.equal(`${newProductData.quantity} in stock`),
        expect(productHeaderSummary.reference).to.contains(newProductData.reference),
      ]);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName = await createProductsPage.getSaveButtonName(page);
      expect(saveButtonName).to.equal(createProductsPage.saveAndPublishButtonName);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue = await basicHelper.percentage(newProductData.priceTaxExcluded, newProductData.tax);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(newProductData.name),
        expect(result.price.toFixed(2)).to.equal((newProductData.priceTaxExcluded + taxValue).toFixed(2)),
        expect(result.summary).to.equal(newProductData.summary),
        expect(result.description).to.equal(newProductData.description),
      ]);
    });

    describe('Create order to check uploaded file', async () => {
      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the product to the cart
        await foProductPage.addProductToTheCart(page, 1);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
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
        expect(isStepAddressComplete, 'Step Address is not complete').eq(true);
      });

      it('should pay the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

        // Go back to BO
        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should update order status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const textResult = await ordersPage.setOrderStatus(page, 1, OrderStatuses.paymentAccepted);
        expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

        // Click on view my shop
        page = await ordersPage.viewMyShop(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').eq(true);
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

        const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
      });

      it('should go to order details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

        await orderHistoryPage.goToDetailsPage(page);

        const pageTitle = await orderDetailsPage.getPageTitle(page);
        expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
      });

      it('should download the file', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

        await orderDetailsPage.clickOnDownloadLink(page);

        const doesFileExist = await files.doesFileExist(newProductData.fileName, 5000);
        expect(doesFileExist, 'File is not downloaded!').eq(true);
      });

      it('should check if the mail is in mailbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkIfMailIsInMailbox', baseContext);

        // 0 : [Mon Shop] Awaiting bank wire payment
        // 1 : [Mon Shop] Order confirmation
        // 2 : [Mon Shop] The virtual product that you bought is available for download
        // 3 : [Mon Shop] Payment accepted
        expect(mails.length).to.be.gte(3);
        expect(mails[2].subject).to.be.equal(
          `[${global.INSTALL.SHOP_NAME}] The virtual product that you bought is available for download`,
        );
        expect(mails[2].text).to.contains('Product(s) to download');
      });
    });
  });

  // 3 - Edit product
  describe('Edit product', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

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

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedProduct', baseContext);

      const createProductMessage = await createProductsPage.setProduct(page, editProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue = await basicHelper.percentage(editProductData.priceTaxExcluded, editProductData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(editProductData.priceTaxExcluded.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(editProductData.priceTaxExcluded + taxValue).toFixed(2)} tax incl. (tax rule: ${editProductData.tax}%)`),
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

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const taxValue = await basicHelper.percentage(editProductData.priceTaxExcluded, editProductData.tax);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(editProductData.name),
        expect(result.price.toFixed(2)).to.equal((editProductData.priceTaxExcluded + taxValue).toFixed(2)),
        expect(result.description).to.equal(editProductData.description),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  // 5 - Delete product
  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await createProductsPage.deleteProduct(page);
      expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_1`);
});
