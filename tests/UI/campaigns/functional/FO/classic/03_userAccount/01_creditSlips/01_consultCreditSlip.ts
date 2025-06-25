import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createAddressTest} from '@commonTests/BO/customers/address';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBasePage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyCreditSlipsPage,
  foClassicMyOrderDetailsPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_creditSlips_consultCreditSlip';

/*
Pre-condition:
 - Create new account on FO
 - Create new address
 - Create order
Scenario:
 - Check there are no credit slips in FO'
 - Create a partial refund from the BO
 - Check there are credit slips in FO
Post condition:
 - Delete created customer
 */
describe('FO - Consult credit slip list & View PDF Credit slip & View order', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;
  let creditSlipID: string;
  let dateIssued: string;
  let filePath: string|null;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const customerData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);
  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);
  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);
  // Pre-Condition: Setup config SMTP
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
    await utilsFile.deleteFile(filePath);
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Consult Credit slip list in FO', async () => {
    describe('Check there are no credit slips in FO', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

        await foClassicHomePage.goTo(page, global.FO.URL);

        const result = await foClassicHomePage.isHomePage(page);
        expect(result).to.eq(true);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

        await foClassicHomePage.goToLoginPage(page);

        const pageTitle = await foClassicLoginPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
      });

      it('should login', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

        await foClassicLoginPage.customerLogin(page, customerData);

        const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage1', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
      });

      it('should go credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goTofoClassicMyCreditSlipsPage1', baseContext);

        await foClassicMyAccountPage.goToCreditSlipsPage(page);

        const pageTitle = await foClassicMyCreditSlipsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyCreditSlipsPage.pageTitle);
      });

      it('should check there no credit slips', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNoCreditSlips', baseContext);

        const alertInfoMessage = await foClassicMyCreditSlipsPage.getAlertInfoMessage(page);
        expect(alertInfoMessage).to.equal(foClassicMyCreditSlipsPage.noCreditSlipsInfoMessage);
      });
    });

    describe('Create a partial refund from the BO', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

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
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

        // View order
        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const result = await boOrdersViewBasePage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
        expect(result).to.equal(dataOrderStatuses.paymentAccepted.name);
      });

      it('should check if the button \'Partial Refund\' is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton', baseContext);

        const result = await boOrdersViewBasePage.isPartialRefundButtonVisible(page);
        expect(result).to.eq(true);
      });

      it('should create \'Partial refund\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createPartialRefund', baseContext);

        await boOrdersViewBasePage.clickOnPartialRefund(page);

        const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(page, 1, 1);
        expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
      });

      it('should check if the mail is in mailbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkIfMailIsInMailbox', baseContext);

        expect(newMail.subject).to.eq(`[${global.INSTALL.SHOP_NAME}] New credit slip regarding your order`);
        expect(newMail.text).to.contains('A credit slip has been generated in your name for order with the reference');
      });

      it('should check if \'Credit slip\' document is created', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

        // Get document name
        const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 3);
        expect(documentType).to.be.equal('Credit slip');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

        // Get document name
        orderReference = await boOrdersViewBasePage.getOrderReference(page);
        expect(orderReference).is.not.equal('');
      });

      it('should get the identifier and the date issued of the credit slip', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getIdentifierDateIssued', baseContext);

        // Get Credit Slip ID
        creditSlipID = await boOrdersViewBlockTabListPage.getFileName(page, 3);
        expect(creditSlipID).is.not.equal('');

        // Get Date Issued
        dateIssued = await boOrdersViewBlockTabListPage.getDocumentDate(page, 3);
        expect(dateIssued).is.not.equal('');
      });
    });

    describe('Check there are credit slips in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

        // View my shop and init pages
        page = await boOrdersViewBasePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage2', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
      });

      it('should go credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goTofoClassicMyCreditSlipsPage2', baseContext);

        await foClassicMyAccountPage.goToCreditSlipsPage(page);

        const pageTitle = await foClassicMyCreditSlipsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyCreditSlipsPage.pageTitle);
      });

      it('should check the number of credit slips', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberCreditSlips', baseContext);

        const numberCreditSlips = await foClassicMyCreditSlipsPage.getNumberOfCreditSlips(page);
        expect(numberCreditSlips).to.equal(1);
      });

      it('should check that the \'Order reference, Credit Slip ID, Date Issued\' are correct', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipInfo', baseContext);

        const creditSlipOrderReference = await foClassicMyCreditSlipsPage.getOrderReference(page, 1);
        expect(creditSlipOrderReference).to.equal(orderReference);

        const creditSlipOrderIdentifier = await foClassicMyCreditSlipsPage.getCreditSlipID(page, 1);
        expect(parseInt(creditSlipOrderIdentifier.replace('#', ''), 10)).to.equal(parseInt(creditSlipID, 10));

        const creditSlipDateIssued = await foClassicMyCreditSlipsPage.getDateIssued(page, 1);
        expect(creditSlipDateIssued).to.equal(dateIssued);
      });

      it('should click on the PDF Icon on the "View credit slip" column', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewCreditSlip', baseContext);

        filePath = await foClassicMyCreditSlipsPage.downloadCreditSlip(page, 1);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });

      it('should check credit slip pdf file', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlip', baseContext);

        // Check Name in pdf
        const isCreditSlip = await utilsFile.isTextInPDF(filePath, 'CREDIT SLIP');
        expect(isCreditSlip, 'Name of the PDF \'CREDIT SLIP\' does not exist in credit slip')
          .to.eq(true);

        // Check Credit Slip ID in pdf
        const creditSlipIDExist = await utilsFile.isTextInPDF(filePath, creditSlipID);
        expect(creditSlipIDExist, `Credit Slip ID ${creditSlipID}' does not exist in credit slip`)
          .to.eq(true);

        // Check DateIssued in pdf
        const dateIssuedExist = await utilsFile.isTextInPDF(filePath, dateIssued);
        expect(dateIssuedExist, `Date Issued '${dateIssued}' does not exist in credit slip`)
          .to.eq(true);

        // Check Order Reference in pdf
        const orderReferenceExist = await utilsFile.isTextInPDF(filePath, orderReference);
        expect(orderReferenceExist, `Order Reference '${orderReference}' does not exist in credit slip`)
          .to.eq(true);

        // Check payment method in pdf
        const paymentMethodExist = await utilsFile.isTextInPDF(filePath, dataPaymentMethods.wirePayment.displayName);
        expect(
          paymentMethodExist,
          `Payment Method '${dataPaymentMethods.wirePayment.displayName}' does not exist in credit slip`,
        ).to.eq(true);
      });

      it('should click on the order Reference link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOrderReferenceLink', baseContext);

        await foClassicMyCreditSlipsPage.clickOrderReference(page, 1);

        const pageTitle = await foClassicMyOrderDetailsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicMyOrderDetailsPage.pageTitle);
      });

      it('should go to credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goTofoClassicMyCreditSlipsPage3', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const myAccountPageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(myAccountPageTitle).to.equal(foClassicMyAccountPage.pageTitle);

        await foClassicMyAccountPage.goToCreditSlipsPage(page);

        const foClassicMyCreditSlipsPageTitle = await foClassicMyCreditSlipsPage.getPageTitle(page);
        expect(foClassicMyCreditSlipsPageTitle).to.equal(foClassicMyCreditSlipsPage.pageTitle);
      });

      it('should click on the "Back to your account" link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickBackToYourAccountLink', baseContext);

        await foClassicMyCreditSlipsPage.clickBackToYourAccountLink(page);

        const myAccountPageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(myAccountPageTitle).to.equal(foClassicMyAccountPage.pageTitle);
      });

      it('should go to credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goTofoClassicMyCreditSlipsPage4', baseContext);

        await foClassicMyAccountPage.goToCreditSlipsPage(page);

        const foClassicMyCreditSlipsPageTitle = await foClassicMyCreditSlipsPage.getPageTitle(page);
        expect(foClassicMyCreditSlipsPageTitle).to.equal(foClassicMyCreditSlipsPage.pageTitle);
      });

      it('should click on the "Home" link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickHomeLink', baseContext);

        await foClassicMyCreditSlipsPage.clickHomeLink(page);

        const homePageTitle = await foClassicHomePage.getPageTitle(page);
        expect(homePageTitle).to.equal(foClassicHomePage.pageTitle);
      });
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
