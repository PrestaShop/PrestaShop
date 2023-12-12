// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/order';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import emailAlertsPage from '@pages/BO/modules/psEmailAlerts';
import {moduleManager} from '@pages/BO/modules/moduleManager';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';

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

const baseContext: string = 'modules_ps_emailalerts_customerNotifications_enableDisableOrderEdit';

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

  // Pre-condition: Create first order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

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
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'Order edit' in the module '${Modules.psEmailAlerts.name}'`, async () => {
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

    it('should enable edit order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEditOrder', baseContext);

      const successMessage = await emailAlertsPage.setNewOrder(page, true);
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('BO: Edit the created order and check emails', async () => {
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

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
    });

    it('should update quantity of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      const quantity = await orderPageProductsBlock.modifyProductQuantity(page, 1, 5);
      expect(quantity, 'Quantity was not updated').to.equal(5);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      newEmailsNumber = allEmails.length;
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });

    it(`should add the product '${Products.demo_14.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCustomizedProduct', baseContext);

      await orderPageProductsBlock.searchProduct(page, Products.demo_14.name);

      const textResult = await orderPageProductsBlock.addProductToCart(page);
      expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail2', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 1);
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });

    it('should delete the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textResult = await orderPageProductsBlock.deleteProduct(page, 1);
      expect(textResult).to.contains(orderPageProductsBlock.successfulDeleteProductMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail3', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your order has been changed`);
    });
  });

  describe(`BO: case 2 - Disable 'Order edit' in the module '${Modules.psEmailAlerts.name}'`, async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });

    it('should disable order edit', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableNewOrder', baseContext);

      const successMessage = await emailAlertsPage.setEditOrder(page, false);
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('BO: Edit the created order and check that no emails received', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
    });

    it('should update quantity of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity2', baseContext);

      const quantity = await orderPageProductsBlock.modifyProductQuantity(page, 1, 4);
      expect(quantity, 'Quantity was not updated').to.equal(4);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail2', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });

    it(`should add the product '${Products.demo_14.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProduct', baseContext);

      await orderPageProductsBlock.searchProduct(page, Products.demo_12.name);

      const textResult = await orderPageProductsBlock.addProductToCart(page);
      expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail3', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });

    it('should delete the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct2', baseContext);

      const textResult = await orderPageProductsBlock.deleteProduct(page, 1);
      expect(textResult).to.contains(orderPageProductsBlock.successfulDeleteProductMessage);
    });

    it('should check that the confirmation mail is not received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail4', baseContext);

      expect(allEmails.length).to.equal(newEmailsNumber + 2);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
