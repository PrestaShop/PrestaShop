// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import administrationPage from '@pages/BO/advancedParameters/administration';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';

// Import data
import Products from '@data/demo/products';

import {
  // Import data
  dataCustomers,
  dataPaymentMethods,
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import {faker} from '@faker-js/faker';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_administration_notifications';

describe('BO - Advanced Parameters - Administration : Check notifications', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createCustomerData: FakerCustomer = new FakerCustomer();
  const messageSend: string = faker.lorem.sentence().substring(0, 35).trim();
  const messageOption: string = `${Products.demo_1.name} (Size: ${Products.demo_1.attributes[0].values[0]} `
    + `- Color: ${Products.demo_1.attributes[1].values[0]})`;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Disable all notifications', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink', baseContext);

      const isNotificationsVisible = await dashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);

      await dashboardPage.clickOnNotificationsTab(page, 'customers');
      await dashboardPage.clickOnNotificationsTab(page, 'messages');
    });

    it('should refresh the page and check that the notifications number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotificationsNumber', baseContext);

      await dashboardPage.reloadPage(page);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.administrationLink,
      );

      const pageTitle = await administrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(administrationPage.pageTitle);
    });

    it('should disable all notifications', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableAllNotifications', baseContext);

      let successMessage = await administrationPage.setShowNotificationsForNewOrders(page, false);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);

      successMessage = await administrationPage.setShowNotificationsForNewCustomers(page, false);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);

      successMessage = await administrationPage.setShowNotificationsForNewMessages(page, false);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
    });

    it('should check that the notifications icon is not visible in the header page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIconNotVisible', baseContext);

      const isLinkVisible = await administrationPage.isNotificationsLinkVisible(page);
      expect(isLinkVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new orders\'', async () => {
    it('should enable new notifications for new orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewOrders', baseContext);

      const successMessage = await administrationPage.setShowNotificationsForNewOrders(page, true);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await dashboardPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO2', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO2', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foLoginPage.goToHomePage(page);
      await homePage.goToProductPage(page, 1);
      await productPage.addProductToTheCart(page);

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

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Close page and init page objects
      page = await orderConfirmationPage.closePage(browserContext, page, 0);
      await shoppingCartsPage.reloadPage(page);

      const pageTitle = await administrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(administrationPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

      const number = await administrationPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink2', baseContext);

      const isNotificationsVisible = await administrationPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number in orders tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsInOrdersTab', baseContext);

      const notificationsNumber = await administrationPage.getNotificationsNumberInTab(page, 'orders');
      expect(notificationsNumber).to.equal(1);
    });

    it('should check that customers tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isCustomersTabInvisible', baseContext);

      const isVisible = await administrationPage.isNotificationsTabVisible(page, 'customers');
      expect(isVisible).to.equal(false);
    });

    it('should check that messages tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isMessagesTabInvisible', baseContext);

      const isVisible = await administrationPage.isNotificationsTabVisible(page, 'messages');
      expect(isVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new customers\'', async () => {
    it('should enable new notifications for new customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewCustomers', baseContext);

      const successMessage = await administrationPage.setShowNotificationsForNewCustomers(page, true);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );
      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
      expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber3', baseContext);

      const number = await customersPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink3', baseContext);

      const isNotificationsVisible = await customersPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number in customers tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsInCustomersTab', baseContext);

      await dashboardPage.clickOnNotificationsTab(page, 'customers');

      const notificationsNumber = await customersPage.getNotificationsNumberInTab(page, 'customers');
      expect(notificationsNumber).to.equal(1);
    });

    it('should refresh the page and check the notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotificationsNumber2', baseContext);

      await dashboardPage.reloadPage(page);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should check that messages tab is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isMessagesTabInvisible2', baseContext);

      const isVisible = await administrationPage.isNotificationsTabVisible(page, 'messages');
      expect(isVisible).to.equal(false);
    });
  });

  describe('Check \'Show notifications for new messages\'', async () => {
    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.administrationLink,
      );

      const pageTitle = await administrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(administrationPage.pageTitle);
    });

    it('should enable new notifications for new messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNotificationsNewMessages', baseContext);

      const successMessage = await administrationPage.setShowNotificationsForNewMessages(page, true);
      expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await dashboardPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('Go to order details and send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const successMessageText = await orderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(orderDetailsPage.successMessageText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await homePage.closePage(browserContext, page, 0);

      await dashboardPage.reloadPage(page);

      const pageTitle = await administrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(administrationPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber4', baseContext);

      const number = await administrationPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink4', baseContext);

      const isNotificationsVisible = await administrationPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check the notifications number in messages tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickMessagesTab', baseContext);

      await administrationPage.clickOnNotificationsTab(page, 'messages');

      const notificationsNumber = await administrationPage.getNotificationsNumberInTab(page, 'messages');
      expect(notificationsNumber).to.equal(1);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest_1`);
});
