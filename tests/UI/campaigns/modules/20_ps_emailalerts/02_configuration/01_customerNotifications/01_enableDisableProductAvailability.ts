// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {disableMerchandiseReturns, enableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {expect} from 'chai';
import {faker} from '@faker-js/faker';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabStocksPage,
  boStockPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type MailDev,
  type MailDevEmail,
  modPsEmailAlertsBoMain,
  type Page,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_configuration_customerNotifications_enableDisableProductAvailability';

describe('Mail alerts module - Customer notifications - Enable/Disable product availability', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  const emailValid: string = faker.internet.email();
  const emailInvalid: string = 'test@test';
  const productData: FakerProduct = new FakerProduct({
    quantity: 0,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productData, `${baseContext}_preTest_1`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_3`);

  describe('BO - Check Configure page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);

      // Check 'Product availability' is set to 'Yes
      const isChecked = await modPsEmailAlertsBoMain.getProductAvailabilityStatus(page);
      expect(isChecked).to.eq(true);
    });
  });

  describe('Case 1: You are not logged to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineNoProductAvailability', baseContext);

      await modPsEmailAlertsBoMain.setProductAvailabilityStatus(page, false);

      const textMessage = await modPsEmailAlertsBoMain.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });

    it('should go to the FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await modPsEmailAlertsBoMain.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should search the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foClassicHomePage.searchProduct(page, productData.name);

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineYesProductAvailability', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);

      await modPsEmailAlertsBoMain.setProductAvailabilityStatus(page, true);

      const textMessage = await modPsEmailAlertsBoMain.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });

    it('should reload the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadProductPage', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should fill the block "Email Alerts" with a valid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockValidEmail', baseContext);

      const textMessage = await foClassicProductPage.notifyEmailAlert(page, emailValid);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationSaved);
    });

    it('should fill the block "Email Alerts" with an invalid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidEmail', baseContext);

      await page.reload();

      const textMessage = await foClassicProductPage.notifyEmailAlert(page, emailInvalid);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with numbers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockNumbers', baseContext);

      const textMessage = await foClassicProductPage.notifyEmailAlert(page, '123456');
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with invalid characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidChars', baseContext);

      const textMessage = await foClassicProductPage.notifyEmailAlert(page, '**¨¨@');
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationEmailInvalid);
    });
  });

  describe('Case 2: You are logged in to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineNoProductAvailability', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);

      await modPsEmailAlertsBoMain.setProductAvailabilityStatus(page, false);

      const textMessage = await modPsEmailAlertsBoMain.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 1);
      await foClassicProductPage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should login on the Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFrontOffice', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicProductPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);
    });

    it('should check the block "Notify when it\' available" is not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockNotPresent', baseContext);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineYesProductAvailability', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);

      await modPsEmailAlertsBoMain.setProductAvailabilityStatus(page, true);

      const textMessage = await modPsEmailAlertsBoMain.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });

    it('should check the block "Notify when it\' available"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockPresent', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should click on  "Notify me when available" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton', baseContext);

      const textMessage = await foClassicProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationSaved);
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'alertAlreadyRegistered', baseContext);

      await page.reload();

      const textMessage = await foClassicProductPage.getBlockMailAlertNotification(page);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationAlreadyRegistered);
    });
  });

  describe('Check email for product availability', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProducts', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', productData.name, 'input');

      const numberOfProductsAfterFilter: number = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);

      const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.eq(productData.name);
    });

    it('should go to product page and update product quantity to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityTo1', baseContext);

      await boProductsPage.goToProductPage(page, 1);
      await boProductsCreateTabStocksPage.setProductQuantity(page, 1);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(numberOfEmails).to.equal(2);
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product available`);
    });

    it('should add the created product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      page = await boProductsCreatePage.changePage(browserContext, 1);
      await page.reload();

      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, 1);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foClassicCheckoutOrderConfirmationPage.goToHomePage(page);

      await foClassicHomePage.searchProduct(page, productData.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);
    });

    it('should click on \'Notify me when available\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton2', baseContext);

      const textMessage = await foClassicProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationSaved);
    });

    it('should go to orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);
      await page.reload();

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(result).to.equal(boOrdersPage.successfulUpdateMessage);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on return products button and type the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts', baseContext);

      await boOrdersViewBlockTabListPage.clickOnReturnProductsButton(page);
      await boOrdersViewBlockProductsPage.checkReturnedQuantity(page);

      const successMessage = await boOrdersViewBlockProductsPage.clickOnReturnProducts(page);
      expect(successMessage).to.eq('The product was successfully returned.');
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail2', baseContext);

      expect(allEmails.length).to.equal(numberOfEmails + 4);
      expect(allEmails[allEmails.length - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product available`);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName2', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', productData.name, 'input');

      const numberOfProductsAfterFilter: number = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);

      const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.eq(productData.name);
    });

    it('should go to product page and update product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityToO', baseContext);

      await boProductsPage.goToProductPage(page, 1);
      await boProductsCreateTabStocksPage.setProductQuantity(page, 0);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should click on \'Notify me when available\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton1', baseContext);

      page = await boProductsCreatePage.changePage(browserContext, 1);
      await page.reload();

      const textMessage = await foClassicProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foClassicProductPage.messageAlertNotificationSaved);
    });

    it('should go to stocks page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.stocksLink,
      );
      await boStockPage.closeSfToolBar(page);

      const pageTitle = await boStockPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStockPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks', baseContext);

      await boStockPage.simpleFilter(page, productData.name);

      const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await boStockPage.updateRowQuantityWithInput(page, 1, 1);
      expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail3', baseContext);

      expect(allEmails.length).to.equal(numberOfEmails + 5);
      expect(allEmails[allEmails.length - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product available`);
    });
  });

  // Post-Condition : Delete created product
  deleteProductTest(productData, `${baseContext}_postTest_1`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_3`);
});
