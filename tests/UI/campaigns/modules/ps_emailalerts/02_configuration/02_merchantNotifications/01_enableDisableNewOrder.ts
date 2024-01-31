// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import emailAlertsPage from '@pages/BO/modules/psEmailAlerts';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage, loginPage} from '@pages/FO/classic/login';
import productPage from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import checkoutPage from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';
import {moduleManager} from '@pages/BO/modules/moduleManager';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'modules_ps_emailalerts_configuration_merchantNotifications_enableDisableNewOrder';

/*
Pre-condition:
- Setup SMTP parameters
Scenario
- Enable new order in email alerts module
- Create order by default customer
- Check 3 emails
- Disable new order in email alerts module
- Create order by default customer
- Check 2 emails
Post-condition:
- Reset SMTP parameters
 */
describe('Mail alerts module - Enable/Disable new order', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;
  let orderID: number;
  let orderReference: string;

  // New order by customer data
  const orderData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 3,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_4`);

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

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'New order' in the module '${Modules.psEmailAlerts.name}'`, async () => {
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
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });

    it('should enable new order and set email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNewOrder', baseContext);

      const successMessage = await emailAlertsPage.setNewOrder(page, true, 'demo@prestashop.com');
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('FO: Create new order', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await viewOrderBasePage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foLoginPage.goToHomePage(page);
      await homePage.goToProductPage(page, orderData.products[0].product.id);
      // Add the product to the cart
      await productPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
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
      await checkoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should close the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeShop', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });
  });

  describe('BO: Get create order ID and reference', async () => {
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

    it('should get the first order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await ordersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should get the first Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

      orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
      expect(orderReference).to.not.eq(null);
    });
  });

  describe('Check emails', async () => {
    it('should get the number of all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfEmails', baseContext);

      numberOfEmails = allEmails.length;
      expect(numberOfEmails).to.not.equal(0);
    });

    it('should check that the order confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderConfirmationMail', baseContext);

      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);
    });

    it('should check that the payment confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMail', baseContext);

      expect(allEmails[numberOfEmails - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting bank wire payment`);
    });

    it('should check that the new order mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewOrderMail', baseContext);

      expect(allEmails[numberOfEmails - 3].subject).to
        .equal(`[${global.INSTALL.SHOP_NAME}] New order : #${orderID} - ${orderReference}`);
    });
  });

  describe(`BO: case 2 - Disable 'New order' in the module '${Modules.psEmailAlerts.name}'`, async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });

    it('should disable new order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns2', baseContext);

      const successMessage = await emailAlertsPage.setNewOrder(page, false);
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('FO: Create second order', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await viewOrderBasePage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, orderData.products[0].product.id);
      // Add the product to the cart
      await productPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
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
      await checkoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Check emails', async () => {
    it('should check the number of emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfEmails', baseContext);

      const number = allEmails.length;
      expect(number).to.equal(numberOfEmails + 2);
    });

    it('should check that the new order confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderConfirmationMail2', baseContext);

      expect(allEmails[allEmails.length - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);
    });

    it('should check that the payment confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMail2', baseContext);

      expect(allEmails[allEmails.length - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting bank wire payment`);
    });

    it('should check that the new order mail is not in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewOrderMailNotExist', baseContext);

      expect(allEmails[allEmails.length - 3].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
