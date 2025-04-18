import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductSettingsPage,
  boProductsCreateTabVirtualProductPage,
  type BrowserContext,
  dataCustomers,
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
  type Page,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_virtualTab';

describe('BO - Catalog - Products : Virtual tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(newProductData.fileName);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(newProductData.fileName);
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

    it('should choose \'Virtual product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct', baseContext);

      const createProductMessage: string|null = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - Create order and download the virtual product
  describe('Create order and download the virtual product', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, 1);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(1);
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

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').eq(true);
    });

    it('should pay the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
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

    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  // 3 - Check the options of out of stock
  describe('Check the options of out of stock', async () => {
    it('should set the product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductQuantity', baseContext);

      await boProductsCreateTabVirtualProductPage.setProductQuantity(page, -100);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    [
      {args: {option: 'Deny orders', isAddToCartButtonVisible: false}},
      {args: {option: 'Allow orders', isAddToCartButtonVisible: true}},
      {args: {option: 'Use default behavior', isAddToCartButtonVisible: false}},
    ].forEach((test, index) => {
      it(`should check the '${test.args.option}' option`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderOption${index}`, baseContext);

        await boProductsCreateTabVirtualProductPage.setOptionWhenOutOfStock(page, test.args.option);

        const createProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should preview product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `previewProduct${index}`, baseContext);

        // Click on preview button
        page = await boProductsCreatePage.previewProduct(page);

        await foClassicProductPage.changeLanguage(page, 'en');

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(newProductData.name);
      });

      it('should check the add to cart button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton${index}`, baseContext);

        const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
        expect(isVisible).to.eq(test.args.isAddToCartButtonVisible);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });
    });

    it('should click on edit default behaviour link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editBehaviour', baseContext);

      page = await boProductsCreateTabVirtualProductPage.clickOnEditDefaultBehaviourLink(page);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await boProductSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should set label when in stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setLabelWhenInStock', baseContext);

      await boProductsCreateTabVirtualProductPage.setProductQuantity(page, 100);
      await boProductsCreateTabVirtualProductPage.setLabelWhenInStock(page, 'Product available');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product availability label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAvailableLabel', baseContext);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Product available');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the allow orders option and set Label when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDenyOrder', baseContext);

      await boProductsCreateTabVirtualProductPage.setProductQuantity(page, -100);
      await boProductsCreateTabVirtualProductPage.setOptionWhenOutOfStock(page, 'Allow orders');
      await boProductsCreateTabVirtualProductPage.setLabelWhenOutOfStock(page, 'Out of stock');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the label of out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLabelOutOfStock', baseContext);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out of stock');

      const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(true);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  // 4 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
