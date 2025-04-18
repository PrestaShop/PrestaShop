import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
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
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyMerchandiseReturnsPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  type MailDev,
  type MailDevEmail,
  modPsEmailAlertsBoMain,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_configuration_merchantNotifications_enableDisableReturn';

/*
Pre-condition:
- Setup SMTP parameters
- Create 2 orders by default customer
- Enable merchandise returns
Scenario
- Enable/Disable returns in email alerts module
- Create merchandise returns
- Check email
Post-condition:
- Disable merchandise returns
- Reset SMTP parameters
 */
describe('Mail alerts module - Enable/Disable return', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;
  let orderID: number;
  let secondOrderID: number;
  let orderReference: string;
  let secondOrderReference: string;

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

  // Pre-condition: Create second order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_2`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_3`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_4`);

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
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'Returns' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
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

    it('should enable returns and set email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setReturns(page, true, 'demo@prestashop.com');
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe(`BO: Change the first created orders status to '${dataOrderStatuses.delivered.name}'`, async () => {
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

    it(`should change the order status to '${dataOrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(result).to.equal(boOrdersPage.successfulUpdateMessage);
    });
  });

  describe(`BO: Change the second created orders status to '${dataOrderStatuses.delivered.name}'`, async () => {
    it('should get the second order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID2', baseContext);

      secondOrderID = await boOrdersPage.getOrderIDNumber(page, 2);
      expect(orderID).to.not.equal(1);
    });

    it('should get the created Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference2', baseContext);

      secondOrderReference = await boOrdersPage.getTextColumn(page, 'reference', 2);
      expect(orderReference).to.not.eq(null);
    });

    it(`should change the order status to '${dataOrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus2', baseContext);

      const result = await boOrdersPage.setOrderStatus(page, 2, dataOrderStatuses.delivered);
      expect(result).to.equal(boOrdersPage.successfulUpdateMessage);
    });
  });

  describe('FO: Create merchandise returns', async () => {
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

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage1', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page, 1);

      const result = await foClassicMyOrderDetailsPage.isOrderReturnFormVisible(page);
      expect(result).to.eq(true);
    });

    it('should create a merchandise return', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'message test');

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      expect(newMail.subject).to.contains(`New return from order #${orderID} - ${orderReference}`);
    });

    it('should close the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeShop', baseContext);

      page = await foClassicMyMerchandiseReturnsPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });
  });

  describe(`BO: case 2 - Disable 'Returns' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
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

    it('should disable returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns2', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setReturns(page, false);
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('FO: Create merchandise returns', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await boOrdersViewBasePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should go to the second order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible2', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page, 2);

      const result = await foClassicMyOrderDetailsPage.isOrderReturnFormVisible(page);
      expect(result).to.eq(true);
    });

    it('should create a merchandise return', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn2', baseContext);

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'message test');

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail2', baseContext);

      expect(newMail.subject).to.not.contains(`New return from order #${secondOrderID} - ${secondOrderReference}`);
    });
  });

  // Post-condition : Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
