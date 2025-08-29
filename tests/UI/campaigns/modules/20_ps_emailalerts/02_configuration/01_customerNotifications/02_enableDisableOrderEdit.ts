// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type MailDev,
  type MailDevEmail,
  modPsEmailAlertsBoMain,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'modules_ps_emailalerts_configuration_customerNotifications_enableDisableOrderEdit';

/*
Pre-condition:
- Setup SMTP parameters
- Create order by default customer
Scenario
- Enable/Disable order edit in email alerts module
- Edit order
- Check email
Post-condition:
- Reset SMTP parameters
 */
describe('Mail alerts module - Enable/Disable return', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let allEmails: MailDevEmail[];
  let newEmailsNumber: number;
  let mailListener: MailDev;

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

  // Pre-condition: Create first order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });

    // Get all emails
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

  describe(`BO: case 1 - Enable 'Order edit' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
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
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should enable edit order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEditOrder', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setEditOrder(page, true);
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('BO: Edit the created order and check emails', async () => {
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

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should update quantity of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      const quantity = await boOrdersViewBlockProductsPage.modifyProductQuantity(page, 1, 5);
      expect(quantity, 'Quantity was not updated').to.equal(5);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      newEmailsNumber = allEmails.length;
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });

    it(`should add the product '${dataProducts.demo_14.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCustomizedProduct', baseContext);

      await boOrdersViewBlockProductsPage.searchProduct(page, dataProducts.demo_14.name);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail2', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 1);
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });

    it('should delete the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textResult = await boOrdersViewBlockProductsPage.deleteProduct(page, 1);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulDeleteProductMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail3', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });
  });

  describe(`BO: case 2 - Disable 'Order edit' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should disable order edit', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableNewOrder', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setEditOrder(page, false);
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('BO: Edit the created order and check that no emails received', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should update quantity of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity2', baseContext);

      const quantity = await boOrdersViewBlockProductsPage.modifyProductQuantity(page, 1, 4);
      expect(quantity, 'Quantity was not updated').to.equal(4);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail4', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });

    it(`should add the product '${dataProducts.demo_14.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProduct', baseContext);

      await boOrdersViewBlockProductsPage.searchProduct(page, dataProducts.demo_12.name);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail5', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });

    it('should delete the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct2', baseContext);

      const textResult = await boOrdersViewBlockProductsPage.deleteProduct(page, 1);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulDeleteProductMessage);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail6', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
