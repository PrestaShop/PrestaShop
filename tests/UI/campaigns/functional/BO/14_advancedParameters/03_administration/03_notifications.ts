import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
  boAdministrationPage,
  boCustomersPage,
  boCustomersCreatePage,
  boDashboardPage,
  boLoginPage,
  boShoppingCartsPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {faker} from '@faker-js/faker';

const baseContext: string = 'functional_BO_advancedParameters_administration_notifications';

describe('BO - Advanced Parameters - Administration : Check notifications', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createCustomerData: FakerCustomer = new FakerCustomer();
  const messageSend: string = faker.lorem.sentence().substring(0, 35).trim();
  const messageOption: string = `${dataProducts.demo_11.name}`;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Disable all notifications', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink', baseContext);

      const isNotificationsVisible = await boDashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);

      await boDashboardPage.clickOnNotificationsTab(page, 'customers');
      await boDashboardPage.clickOnNotificationsTab(page, 'messages');
    });

    it('should refresh the page and check that the notifications number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotificationsNumber', baseContext);

      await boDashboardPage.reloadPage(page);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.administrationLink,
      );

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should disable all notifications', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableAllNotifications', baseContext);

      let successMessage = await boAdministrationPage.setShowNotificationsForNewOrders(page, false);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);

      successMessage = await boAdministrationPage.setShowNotificationsForNewCustomers(page, false);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);

      successMessage = await boAdministrationPage.setShowNotificationsForNewMessages(page, false);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should check that the notifications icon is not visible in the header page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIconNotVisible', baseContext);

      const isLinkVisible = await boAdministrationPage.isNotificationsLinkVisible(page);
      expect(isLinkVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new orders\'', async () => {
    it('should enable new notifications for new orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewOrders', baseContext);

      const successMessage = await boAdministrationPage.setShowNotificationsForNewOrders(page, true);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boDashboardPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO2', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO2', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicLoginPage.goToHomePage(page);
      await foClassicHomePage.goToProductPage(page, 1);
      await foClassicProductPage.addProductToTheCart(page);

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

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Close page and init page objects
      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);
      await boShoppingCartsPage.reloadPage(page);

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

      const number = await boAdministrationPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink2', baseContext);

      const isNotificationsVisible = await boAdministrationPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number in orders tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsInOrdersTab', baseContext);

      const notificationsNumber = await boAdministrationPage.getNotificationsNumberInTab(page, 'orders');
      expect(notificationsNumber).to.equal(1);
    });

    it('should check that customers tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isCustomersTabInvisible', baseContext);

      const isVisible = await boAdministrationPage.isNotificationsTabVisible(page, 'customers');
      expect(isVisible).to.equal(false);
    });

    it('should check that messages tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isMessagesTabInvisible', baseContext);

      const isVisible = await boAdministrationPage.isNotificationsTabVisible(page, 'messages');
      expect(isVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new customers\'', async () => {
    it('should enable new notifications for new customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewCustomers', baseContext);

      const successMessage = await boAdministrationPage.setShowNotificationsForNewCustomers(page, true);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );
      await boCustomersPage.closeSfToolBar(page);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await boCustomersPage.goToAddNewCustomerPage(page);

      const pageTitle = await boCustomersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await boCustomersCreatePage.createEditCustomer(page, createCustomerData);
      expect(textResult).to.equal(boCustomersPage.successfulCreationMessage);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber3', baseContext);

      const number = await boCustomersPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink3', baseContext);

      const isNotificationsVisible = await boCustomersPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number in customers tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsInCustomersTab', baseContext);

      await boDashboardPage.clickOnNotificationsTab(page, 'customers');

      const notificationsNumber = await boCustomersPage.getNotificationsNumberInTab(page, 'customers');
      expect(notificationsNumber).to.equal(1);
    });

    it('should refresh the page and check the notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotificationsNumber2', baseContext);

      await boDashboardPage.reloadPage(page);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should check that messages tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isMessagesTabInvisible2', baseContext);

      const isVisible = await boAdministrationPage.isNotificationsTabVisible(page, 'messages');
      expect(isVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new messages\'', async () => {
    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.administrationLink,
      );

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should enable new notifications for new messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewMessages', baseContext);

      const successMessage = await boAdministrationPage.setShowNotificationsForNewMessages(page, true);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await boDashboardPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('Go to order details and send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const successMessageText = await foClassicMyOrderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(foClassicMyOrderDetailsPage.successMessageText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      await boDashboardPage.reloadPage(page);

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber4', baseContext);

      const number = await boAdministrationPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink4', baseContext);

      const isNotificationsVisible = await boAdministrationPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check the notifications number in messages tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickMessagesTab', baseContext);

      await boAdministrationPage.clickOnNotificationsTab(page, 'messages');

      const notificationsNumber = await boAdministrationPage.getNotificationsNumberInTab(page, 'messages');
      expect(notificationsNumber).to.equal(1);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest_1`);
});
