// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';
import {disableMerchandiseReturns, enableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psEmailAlerts from '@pages/BO/modules/psEmailAlerts';
import ordersPage from '@pages/BO/orders';
import productsPage from '@pages/BO/catalog/products';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import addProductPage from '@pages/BO/catalog/products/add';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import stocksPage from '@pages/BO/catalog/stocks';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import Modules from '@data/demo/modules';
import ProductData from '@data/faker/product';
import MailDevEmail from '@data/types/maildevEmail';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import {faker} from '@faker-js/faker';
import type {BrowserContext, Page} from 'playwright';
import MailDev from 'maildev';

const baseContext: string = 'modules_ps_emailalerts_configuration_customerNotifications_enableDisableProductAvailability';

describe('Mail alerts module - Customer notifications - Enable/Disable product availability', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  const emailValid: string = faker.internet.email();
  const emailInvalid: string = 'test@test';
  const productData: ProductData = new ProductData({
    quantity: 0,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productData, `${baseContext}_preTest_1`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_3`);

  describe('BO - Check Configure page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      // Check 'Product availability' is set to 'Yes
      const isChecked = await psEmailAlerts.getProductAvailabilityStatus(page);
      expect(isChecked).to.eq(true);
    });
  });

  describe('Case 1: You are not logged to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineNoProductAvailability', baseContext);

      await psEmailAlerts.setProductAvailabilityStatus(page, false);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should go to the FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await psEmailAlerts.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should search the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, productData.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineYesProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, true);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should reload the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadProductPage', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should fill the block "Email Alerts" with a valid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockValidEmail', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, emailValid);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should fill the block "Email Alerts" with an invalid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidEmail', baseContext);

      await page.reload();

      const textMessage = await foProductPage.notifyEmailAlert(page, emailInvalid);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with numbers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockNumbers', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, '123456');
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with invalid characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidChars', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, '**¨¨@');
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });
  });

  describe('Case 2: You are logged in to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineNoProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, false);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await foProductPage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should login on the Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFrontOffice', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foProductPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);
    });

    it('should check the block "Notify when it\' available" is not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockNotPresent', baseContext);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineYesProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, true);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should check the block "Notify when it\' available"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockPresent', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should click on  "Notify me when available" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'alertAlreadyRegistered', baseContext);

      await page.reload();

      const textMessage = await foProductPage.getBlockMailAlertNotification(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationAlreadyRegistered);
    });
  });

  describe('Check email for product availability', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProducts', baseContext);

      await productsPage.filterProducts(page, 'product_name', productData.name, 'input');

      const numberOfProductsAfterFilter: number = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);

      const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.eq(productData.name);
    });

    it('should go to product page and update product quantity to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityTo1', baseContext);

      await productsPage.goToProductPage(page, 1);
      await stocksTab.setProductQuantity(page, 1);

      const message = await addProductPage.saveProduct(page);
      expect(message).to.eq(addProductPage.successfulUpdateMessage);
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(numberOfEmails).to.equal(2);
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product available`);
    });

    it('should add the created product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      page = await addProductPage.changePage(browserContext, 1);
      await page.reload();

      // Add the product to the cart
      await foProductPage.addProductToTheCart(page, 1);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await orderConfirmationPage.goToHomePage(page);

      await homePage.searchProduct(page, productData.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);
    });

    it('should click on \'Notify me when available\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton2', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should go to orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      page = await foProductPage.changePage(browserContext, 0);
      await page.reload();

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await ordersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(result).to.equal(ordersPage.successfulUpdateMessage);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on return products button and type the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts', baseContext);

      await orderPageTabListBlock.clickOnReturnProductsButton(page);
      await orderPageProductsBlock.checkReturnedQuantity(page);

      const successMessage = await orderPageProductsBlock.clickOnReturnProducts(page);
      expect(successMessage).to.eq('The product was successfully returned.');
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail2', baseContext);

      expect(allEmails.length).to.equal(numberOfEmails + 4);
      expect(allEmails[allEmails.length - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product available`);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName2', baseContext);

      await productsPage.filterProducts(page, 'product_name', productData.name, 'input');

      const numberOfProductsAfterFilter: number = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);

      const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.eq(productData.name);
    });

    it('should go to product page and update product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityToO', baseContext);

      await productsPage.goToProductPage(page, 1);
      await stocksTab.setProductQuantity(page, 0);

      const message = await addProductPage.saveProduct(page);
      expect(message).to.eq(addProductPage.successfulUpdateMessage);
    });

    it('should click on \'Notify me when available\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton1', baseContext);

      page = await addProductPage.changePage(browserContext, 1);
      await page.reload();

      const textMessage = await foProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should go to stocks page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );
      await stocksPage.closeSfToolBar(page);

      const pageTitle = await stocksPage.getPageTitle(page);
      expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it(`should filter by name '${productData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks', baseContext);

      await stocksPage.simpleFilter(page, productData.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 1, 1);
      expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
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
