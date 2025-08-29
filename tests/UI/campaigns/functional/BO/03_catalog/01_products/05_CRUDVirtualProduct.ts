import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicProductPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsCore,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_CRUDVirtualProduct';

describe('BO - Catalog - Products : CRUD virtual product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let mailListener: MailDev;

  // Data to create standard product
  const mails: MailDevEmail[] = [];
  const newProductData: FakerProduct = new FakerProduct({
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
  const editProductData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
    await utilsFile.generateImage(newProductData.fileName);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      mails.push(email);
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
    await utilsFile.deleteFile(newProductData.fileName);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).eq(true);
    });

    it('should choose \'Virtual product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the virtual product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDescription', baseContext);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.virtualProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFoProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - View product
  describe('View product in BO & FO', async () => {
    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue = utilsCore.percentage(newProductData.priceTaxExcluded, newProductData.tax);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
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

      const saveButtonName = await boProductsCreatePage.getSaveButtonName(page);
      expect(saveButtonName).to.equal(boProductsCreatePage.saveAndPublishButtonName);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue = utilsCore.percentage(newProductData.priceTaxExcluded, newProductData.tax);

      const result = await foClassicProductPage.getProductInformation(page);
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
        await foClassicProductPage.addProductToTheCart(page, 1);

        const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should proceed to checkout and sign in', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').eq(true);
      });

      it('should pay the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should update order status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.paymentAccepted);
        expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

        // Click on view my shop
        page = await boOrdersPage.viewMyShop(page);

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
      });

      it('should go to order history page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

        const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
      });

      it('should go to order details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

        await foClassicMyOrderHistoryPage.goToDetailsPage(page);

        const pageTitle = await foClassicMyOrderDetailsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyOrderDetailsPage.pageTitle);
      });

      it('should download the file', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

        await foClassicMyOrderDetailsPage.clickOnDownloadLink(page);

        const doesFileExist = await utilsFile.doesFileExist(newProductData.fileName, 5000);
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
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await boProductsPage.goToProductPage(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, editProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue = utilsCore.percentage(editProductData.priceTaxExcluded, editProductData.tax);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
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
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const taxValue = utilsCore.percentage(editProductData.priceTaxExcluded, editProductData.tax);

      const result = await foClassicProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(editProductData.name),
        expect(result.price.toFixed(2)).to.equal((editProductData.priceTaxExcluded + taxValue).toFixed(2)),
        expect(result.description).to.equal(editProductData.description),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  // 5 - Delete product
  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_1`);
});
