import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerServicePage,
  boCustomerServiceViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerContactMessage,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicContactUsPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicMyAccountPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const contactUsData: FakerContactMessage = new FakerContactMessage({
    subject: 'Customer service',
    message: 'Test message to customer service for order reference',
    emailAddress: dataCustomers.johnDoe.email,
    reference: '',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create a file for upload in the "contact us" form
    await Promise.all([
      utilsFile.createFile('.', filename, `test ${filename}`),
    ]);
  });

  after(async () => {
    // Delete created file
    await Promise.all([
      utilsFile.deleteFile(filename),
    ]);

    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to shop FO and make a order', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);
      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);
      const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicHomePage.goToHomePage(page);
      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicHomePage.quickViewProduct(page, 1);
      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should check the cart details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

      const result = await foClassicCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_1.name),
        expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);

      const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
        page,
        foClassicCheckoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay by back wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const pageTitle = await foClassicCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('recover the command reference number and go to the "contact us" page', async () => {
    it('should get the order reference value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReferenceValue', baseContext);

      contactUsData.reference = await foClassicCheckoutOrderConfirmationPage.getOrderReferenceValue(page);
      contactUsData.message += ` ${contactUsData.reference}`;
      expect(contactUsData.reference).to.not.have.lengthOf(0);
    });

    it('should go to the "contact us" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheContactUsPage', baseContext);

      await foClassicCheckoutOrderConfirmationPage.goToContactUsPage(page);

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicContactUsPage.pageTitle);
    });
  });

  describe('check the pre-filled email field, select order, and send the message', async () => {
    it('should check the pre-filled email field', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTheFormInfos', baseContext);

      const emailFieldValue = await foClassicContactUsPage.getEmailFieldValue(page);
      expect(emailFieldValue).to.contains(dataCustomers.johnDoe.email);
    });

    it('should send the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTheMessage', baseContext);

      await foClassicContactUsPage.sendMessage(page, contactUsData, filename);

      const sendMessageSuccessAlert = await foClassicContactUsPage.getAlertSuccess(page);
      expect(sendMessageSuccessAlert).to.contains(foClassicContactUsPage.validationMessage);
    });
  });

  describe('Go to BO and check if the message is visible and the information are correct', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });

    it('should go to the message detailed view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMessageView', baseContext);

      await boCustomerServicePage.goToViewMessagePage(page);

      const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
    });

    it('should check the message content and uploaded file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageContentAndFile', baseContext);

      const messageContent = await boCustomerServiceViewPage.getCustomerMessage(page);
      expect(messageContent).to.contains(contactUsData.message);
      expect(messageContent).to.contains('Attachment');
    });

    it('should go back to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCustomerServicePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const deleteMessageSuccessText = await boCustomerServicePage.deleteMessage(page, 1);
      expect(deleteMessageSuccessText).to.contains(boCustomerServicePage.deleteMessageSuccessAlertText);
    });
  });
});
