import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boOrdersPage,
  boOrdersViewBasePage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type MailDev,
  type MailDevEmail,
  modPsEmailAlertsBoMain,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 3,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_4`);

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

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'New order' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageEnableNewOrder', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleEnableNewOrder', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPageEnableNewOrder', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should enable new order and set email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNewOrder', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setNewOrder(page, true, 'demo@prestashop.com');
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('FO: Create new order', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boOrdersViewBasePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicLoginPage.goToHomePage(page);
      await foClassicHomePage.goToProductPage(page, orderData.products[0].product.id);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
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
      await foClassicCheckoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should close the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeShop', baseContext);

      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });
  });

  describe('BO: Get create order ID and reference', async () => {
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

    it('should get the first order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await boOrdersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should get the first Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

      orderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
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

  describe(`BO: case 2 - Disable 'New order' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
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
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should disable new order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns2', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setNewOrder(page, false);
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('FO: Create second order', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await boOrdersViewBasePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      // Go to home page
      await foClassicLoginPage.goToHomePage(page);
      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, orderData.products[0].product.id);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep2', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep2', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder2', baseContext);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
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
