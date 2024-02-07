// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerServiceMainPage from '@pages/BO/customerService/customerService';
import customerServiceMessageViewPage from '@pages/BO/customerService/customerService/view';
// FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {cartPage} from '@pages/FO/classic/cart';
import {contactUsPage} from '@pages/FO/classic/contactUs';
import {checkoutPage} from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import PaymentMethods from '@data/demo/paymentMethods';
import MessageData from '@data/faker/message';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_classic_orderConfirmation_contactUs';

/*
1 GO to shop FO
2 login
3 make an order
4 in the 'order confirmation' page recover the command reference number and click on the 'contact support' link
5 In the 'contact us' page check if the pre-filled infos are correct
9 send the message
10 go to the shop BO
11 go to the "customer service" page
12 check that the previously made message is visible and the infos are correct
*/
describe('FO - Order confirmation : Contact us', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const filename: string = 'testfile.txt';

  const contactUsData: MessageData = new MessageData({
    subject: 'Customer service',
    message: 'Test message to customer service for order reference',
    emailAddress: Customers.johnDoe.email,
    reference: '',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create a file for upload in the "contact us" form
    await Promise.all([
      files.createFile('.', filename, `test ${filename}`),
    ]);
  });

  after(async () => {
    // Delete created file
    await Promise.all([
      files.deleteFile(filename),
    ]);

    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to shop FO and make a order', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);
      const result = await foHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHomePage.goToHomePage(page);
      const result = await foHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.addProductToCartByQuickView(page, 1, 1);
      await foHomePage.proceedToCheckout(page);
      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check the cart details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

      const result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);

      const isStepPersonalInformationComplete = await checkoutPage.isStepCompleted(
        page,
        checkoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay by back wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('recover the command reference number and go to the "contact us" page', async () => {
    it('should get the order reference value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReferenceValue', baseContext);

      contactUsData.reference = await orderConfirmationPage.getOrderReferenceValue(page);
      contactUsData.message += ` ${contactUsData.reference}`;
      expect(contactUsData.reference).to.not.have.lengthOf(0);
    });

    it('should go to the "contact us" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheContactUsPage', baseContext);

      await orderConfirmationPage.goToContactUsPage(page);

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle).to.contains(contactUsPage.pageTitle);
    });
  });

  describe('check the pre-filled email field, select order, and send the message', async () => {
    it('should check the pre-filled email field', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTheFormInfos', baseContext);

      const emailFieldValue = await contactUsPage.getEmailFieldValue(page);
      expect(emailFieldValue).to.contains(Customers.johnDoe.email);
    });

    it('should send the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTheMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, filename);

      const sendMessageSuccessAlert = await contactUsPage.getAlertSuccess(page);
      expect(sendMessageSuccessAlert).to.contains(contactUsPage.validationMessage);
    });
  });

  describe('Go to BO and check if the message is visible and the information are correct', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServiceMainPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServiceMainPage.pageTitle);
    });

    it('should go to the message detailed view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMessageView', baseContext);

      await customerServiceMainPage.goToViewMessagePage(page);

      const pageTitle = await customerServiceMessageViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServiceMessageViewPage.pageTitle);
    });

    it('should check the message content and uploaded file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageContentAndFile', baseContext);

      const messageContent = await customerServiceMessageViewPage.getCustomerMessage(page);
      expect(messageContent).to.contains(contactUsData.message);
      expect(messageContent).to.contains('Attachment');
    });

    it('should go back to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCustomerServicePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServiceMainPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServiceMainPage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const deleteMessageSuccessText = await customerServiceMainPage.deleteMessage(page, 1);
      expect(deleteMessageSuccessText).to.contains(customerServiceMainPage.deleteMessageSuccessAlertText);
    });
  });
});
