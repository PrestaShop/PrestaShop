// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';
import date from '@utils/date';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {disableMerchandiseReturns, enableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import pages
// Import BO pages
import merchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
import editMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns/edit';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import foMerchandiseReturnsPage from '@pages/FO/classic/myAccount/merchandiseReturns';
import orderDetailsPage from '@pages/FO/classic/myAccount/orderDetails';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderReturnStatuses from '@data/demo/orderReturnStatuses';
import Addresses from '@data/demo/address';
import OrderData from '@data/faker/order';
import CustomerData from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'functional_BO_customerService_merchandiseReturns_updateStatus';

/*
Pre-condition:
- Create order in FO
- Activate merchandise returns
- Change the first order status in the list to shipped
- Setup SMTP config
Scenario
- Create merchandise returns in FO
- GO to BO > merchandise returns page > Edit
- Test all return statuses
- Check received emails
Post-condition:
- Deactivate merchandise returns
- Reset SMTP config
 */
describe('BO - Customer Service - Merchandise Returns : Update status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string | null;
  let returnID: number;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;
  const todayDate: string = date.getDateFormat('mm/dd/yyyy');
  const orderData: OrderData = new OrderData({
    customer: CustomerData.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // get all emails
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

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_1`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_2`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_3`);

  describe('PRE-TEST: Change order status to \'Shipped\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

      const pageTitle = await viewOrderBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderBasePage.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await viewOrderBasePage.isReturnProductsButtonVisible(page);
      expect(result).to.eq(true);
    });
  });

  describe('FO : Create merchandise returns', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await viewOrderBasePage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await homePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, CustomerData.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      await orderHistoryPage.goToDetailsPage(page, 1);

      const result = await orderDetailsPage.isOrderReturnFormVisible(page);
      expect(result).to.eq(true);
    });

    it('should create a merchandise return', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await orderDetailsPage.requestMerchandiseReturn(page, 'test', 1, [{quantity: 1}]);

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await orderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await viewOrderBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
    });
  });

  describe('BO : Update returns status', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should get the return number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getReturnNumber', baseContext);

      returnID = parseInt(await merchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order_return'), 10);
      expect(returnID).to.not.equal(0);
    });

    const tests = [
      {args: {status: OrderReturnStatuses.waitingForPackage.name}},
      {args: {status: OrderReturnStatuses.packageReceived.name}},
      {args: {status: OrderReturnStatuses.returnDenied.name}},
      {args: {status: OrderReturnStatuses.returnCompleted.name}},
    ];
    tests.forEach((test, index: number) => {
      describe(`Update returns status to ${test.args.status} and check result`, async () => {
        it('should go to edit merchandise returns page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToEditReturnsPage${index}`, baseContext);

          await merchandiseReturnsPage.goToMerchandiseReturnPage(page);

          const pageTitle = await editMerchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(editMerchandiseReturnsPage.pageTitle);
        });

        it('should update the status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `editStatus${index}`, baseContext);

          const textResult = await editMerchandiseReturnsPage.setStatus(page, test.args.status, true);
          expect(textResult).to.contains(editMerchandiseReturnsPage.successfulUpdateMessage);
        });

        it('should check the confirmation email subject', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkConfirmationEmail${index}`, baseContext);

          numberOfEmails = allEmails.length;
          expect(allEmails[numberOfEmails - 1].subject)
            .to.equal(`[${global.INSTALL.SHOP_NAME}] Your order return status has changed`);
        });

        it('should check the confirmation email text', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkConfirmationEmailText${index}`, baseContext);

          numberOfEmails = allEmails.length;
          expect(allEmails[numberOfEmails - 1].text)
            .to.contains('We have updated the progress on your return')
            .and.to.contains(`the new status is: "${test.args.status}".`);
        });

        if (test.args.status === OrderReturnStatuses.waitingForPackage.name) {
          it('should download and check the existence of the PDF print out file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkPDF', baseContext);

            filePath = await editMerchandiseReturnsPage.downloadPDF(page);

            const exist = await files.doesFileExist(filePath);
            expect(exist, 'File does not exist').to.eq(true);
          });

          it('should check the file name', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkFileName', baseContext);

            const fileName = await editMerchandiseReturnsPage.getFileName(page);
            expect(fileName).to.eq('Print out');
          });

          it('should check the header of the return PDF', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnPDF', baseContext);

            let returnPrefix = '#RE00000';

            if (returnID >= 10) {
              returnPrefix = '#RE0000';
            }
            const isVisible = await files.isTextInPDF(filePath, `ORDER RETURN,,${todayDate},,${returnPrefix}${returnID}`);
            expect(isVisible, 'The header of the PDF is not correct!').to.eq(true);
          });

          it('should check the billing address in the PDF', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress', baseContext);

            const billingAddressExist = await files.isTextInPDF(
              filePath,
              'Billing & Delivery Address,,'
              + `${Addresses.second.firstName} ${Addresses.second.lastName},`
              + `${Addresses.second.company},`
              + `${Addresses.second.address},`
              + `${Addresses.second.secondAddress},`
              + `${Addresses.second.postalCode} ${Addresses.second.city},`
              + `${Addresses.second.country},`
              + `${Addresses.second.phone}`,
            );
            expect(billingAddressExist, 'Billing address is not correct in PDF!').to.eq(true);
          });

          it('should check the return number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkReturnNumber', baseContext);

            let returnPrefix = '00000';

            if (returnID >= 10) {
              returnPrefix = '0000';
            }

            const isVisible = await files.isTextInPDF(
              filePath,
              'We have logged your return request.,Your package must be returned to us within 14 days of receiving your order.'
              + `,,Return Number, ,Date,,${returnPrefix}${returnID}, ,${todayDate}`);
            expect(isVisible, 'Order return ID and the date are not correct!').to.eq(true);
          });

          it('should check the returned product details', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedProduct', baseContext);

            const isVisible = await files.isTextInPDF(
              filePath,
              `Items to be returned, ,Reference, ,Qty,,${Products.demo_1.name} (Size: S - Color: White), ,`
              + `${Products.demo_1.reference}, ,1`);
            expect(isVisible, 'Returned product details are not correct!').to.eq(true);
          });
        } else {
          it('should check that the file is not existing', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkFileNotExisting${index}`, baseContext);

            const fileName = await editMerchandiseReturnsPage.getFileName(page);
            expect(fileName).to.eq('--');
          });
        }
        it('should click on cancel button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `clickOnCancelButton${index}`, baseContext);

          await editMerchandiseReturnsPage.clickOnCancelButton(page);

          const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
        });

        it('should check the updated status in the merchandise returns table', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatus${index}`, baseContext);

          const status = await merchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'name');
          expect(status).to.eq(test.args.status);
        });
      });
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
