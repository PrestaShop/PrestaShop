import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderHistoryPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_updateStatus';

/*
Pre-condition:
- Create order in FO, choose payment method bank wire payment
- Setup SMTP parameters
Scenario:
- Go to BO orders page and change order status to 'Canceled'
- Go to FO and check the new order status
- Go to BO orders page and change order status to 'Payment accepted'
- Check invoice creation
- Download invoice from list and check pdf text
- Go to FO and check the new order status and the invoice
- Go to BO orders page and change order status to 'Delivered'
- Go to FO and check the new order status
Post-condition:
- Reset SMTP parameters
 */
describe('BO - orders : Update order status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string | null;
  let orderId: number;
  let allEmails: MailDevEmail[];
  let mailListener: MailDev;

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

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

  describe('Go to \'Orders > Orders\' page', async () => {
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

    it('should reset filter and get the last order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boOrdersPage.resetFilter(page);

      const result = await boOrdersPage.getTextColumn(page, 'id_order', 1);
      orderId = parseInt(result, 10);
      expect(orderId).to.be.at.least(1);
    });
  });

  describe('Change orders status in BO then check it in FO', async () => {
    [
      {args: {orderStatus: dataOrderStatuses.canceled, email: 'Canceled'}},
      {args: {orderStatus: dataOrderStatuses.refunded, email: 'Refunded'}},
      {args: {orderStatus: dataOrderStatuses.onBackorderNotPaid, email: 'On backorder (not paid)'}},
      {args: {orderStatus: dataOrderStatuses.onBackorderPaid, email: 'On backorder (paid)'}},
      {args: {orderStatus: dataOrderStatuses.paymentAccepted, email: 'Payment accepted'}},
      {args: {orderStatus: dataOrderStatuses.shipped, email: 'Shipped'}},
    ].forEach((test, index: number) => {
      describe(`Change orders status to '${test.args.orderStatus.name}' in BO`, async () => {
        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

          const textResult = await boOrdersPage.setOrderStatus(page, 1, test.args.orderStatus);
          expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatusBO${index}`, baseContext);

          const orderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
          expect(orderStatus, 'Order status was not updated').to.equal(test.args.orderStatus.name);
        });

        it('should check received email', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkEmail${index}`, baseContext);

          expect(allEmails[allEmails.length - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] ${test.args.email}`);
        });

        if (test.args.orderStatus.name === dataOrderStatuses.paymentAccepted.name) {
          it('should download invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

            filePath = await boOrdersPage.downloadInvoice(page, 1);

            const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
            expect(doesFileExist, 'The file is not existing!').to.eq(true);
          });

          it('should check invoice pdf file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceText', baseContext);

            // Get order information
            const orderInformation = await boOrdersPage.getOrderFromTable(page, 1);

            // Check Reference in pdf
            const referenceExist = await utilsFile.isTextInPDF(filePath, orderInformation.reference);
            expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in invoice`)
              .to.eq(true);

            // Check country name in delivery Address in pdf
            const deliveryExist = await utilsFile.isTextInPDF(filePath, orderInformation.delivery);
            expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in invoice`)
              .to.eq(true);

            // Check customer name in pdf
            const customerExist = await utilsFile.isTextInPDF(filePath, orderInformation.customer.slice(3));
            expect(customerExist, `Customer name '${orderInformation.customer}' does not exist in invoice`)
              .to.eq(true);

            // Check total paid in pdf
            const totalPaidExist = await utilsFile.isTextInPDF(filePath, orderInformation.totalPaid);
            expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in invoice`)
              .to.eq(true);
          });
        }

        if (test.args.orderStatus.name === dataOrderStatuses.delivered.name) {
          it('should download delivery slip', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);

            filePath = await boOrdersPage.downloadDeliverySlip(page, 1);

            const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
            expect(doesFileExist).to.eq(true);
          });

          it('should check delivery slip pdf file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlip', baseContext);

            // Get delivery slip information
            const orderInformation = await boOrdersPage.getOrderFromTable(page, 1);

            // Check Reference in pdf
            const referenceExist = await utilsFile.isTextInPDF(filePath, orderInformation.reference);

            expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in delivery slip`)
              .to.eq(true);

            // Check country name in delivery Address in pdf
            const deliveryExist = await utilsFile.isTextInPDF(filePath, orderInformation.delivery);
            expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in delivery slip`)
              .to.eq(true);

            // Check customer name in pdf
            const customerExist = await utilsFile.isTextInPDF(filePath, orderInformation.customer.slice(3));
            expect(customerExist, `Country name '${orderInformation.customer}' does not exist in delivery slip`)
              .to.eq(true);

            // Check total paid in pdf
            const totalPaidExist = await utilsFile.isTextInPDF(filePath, orderInformation.totalPaid);
            expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in delivery slip`)
              .to.eq(true);
          });
        }
      });

      describe('Check the order status in FO ', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFoToCheckStatus${index}`, baseContext);

          page = await boOrdersPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open FO home page').to.eq(true);
        });

        if (index === 0) {
          it('should go to login page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToLoginPage${index}`, baseContext);

            await foClassicHomePage.goToLoginPage(page);

            const pageTitle = await foClassicLoginPage.getPageTitle(page);
            expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
          });

          it('should sign in with default customer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `sighInFoToCheckStatus${index}`, baseContext);

            await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

            const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
            expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
          });
        }
        it('should go to orders history page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderHistoryPage${index}`, baseContext);

          await foClassicHomePage.goToMyAccountPage(page);
          await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

          const pageTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
          expect(pageTitle, 'Fail to open order history page').to.contains(foClassicMyOrderHistoryPage.pageTitle);
        });

        it('should check the last order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkLastOrderStatus${index}`, baseContext);

          const orderStatusFO = await foClassicMyOrderHistoryPage.getOrderStatus(page, 1);
          expect(orderStatusFO, 'Order status is not correct').to.equal(test.args.orderStatus.name);
        });

        if (test.args.orderStatus.name === dataOrderStatuses.paymentAccepted.name) {
          it('should check if the last invoice is visible', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkLastInvoice${index}`, baseContext);

            const isVisible = await foClassicMyOrderHistoryPage.isInvoiceVisible(page, 1);
            expect(isVisible, 'The invoice file is not existing!').to.eq(true);
          });

          it('should check the order ID of the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkOrderID${index}`, baseContext);

            const orderID = await foClassicMyOrderHistoryPage.getOrderIdFromInvoiceHref(page, 1);
            expect(orderID, 'The invoice file attached is not correct!').to.contains(`id_order=${orderId}`);
          });
        }

        it('should close the shop page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `closeShop${index}`, baseContext);

          page = await foClassicMyOrderHistoryPage.closePage(browserContext, page, 0);

          const pageTitle = await boOrdersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrdersPage.pageTitle);
        });
      });
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
