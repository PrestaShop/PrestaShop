import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {disableMerchandiseReturns, enableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  boMerchandiseReturnsEditPage,
  boOrdersPage,
  boOrdersViewBasePage,
  type BrowserContext,
  dataAddresses,
  dataCustomers,
  dataOrderReturnStatuses,
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
  type Page,
  utilsDate,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const todayDate: string = utilsDate.getDateFormat('mm/dd/yyyy');
  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

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

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_1`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_2`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_3`);

  describe('PRE-TEST: Change order status to \'Shipped\'', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBasePage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await boOrdersViewBasePage.isReturnProductsButtonVisible(page);
      expect(result).to.eq(true);
    });
  });

  describe('FO : Create merchandise returns', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boOrdersViewBasePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

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

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'test', 1, [{quantity: 1}]);

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });
  });

  describe('BO : Update returns status', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should get the return number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getReturnNumber', baseContext);

      returnID = parseInt(await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order_return'), 10);
      expect(returnID).to.not.equal(0);
    });

    const tests = [
      {args: {status: dataOrderReturnStatuses.waitingForPackage.name}},
      {args: {status: dataOrderReturnStatuses.packageReceived.name}},
      {args: {status: dataOrderReturnStatuses.returnDenied.name}},
      {args: {status: dataOrderReturnStatuses.returnCompleted.name}},
    ];
    tests.forEach((test, index: number) => {
      describe(`Update returns status to ${test.args.status} and check result`, async () => {
        it('should go to edit merchandise returns page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToEditReturnsPage${index}`, baseContext);

          await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

          const pageTitle = await boMerchandiseReturnsEditPage.getPageTitle(page);
          expect(pageTitle).to.contains(boMerchandiseReturnsEditPage.pageTitle);
        });

        it('should update the status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `editStatus${index}`, baseContext);

          const textResult = await boMerchandiseReturnsEditPage.setStatus(page, test.args.status, true);
          expect(textResult).to.contains(boMerchandiseReturnsEditPage.successfulUpdateMessage);
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

        if (test.args.status === dataOrderReturnStatuses.waitingForPackage.name) {
          it('should download and check the existence of the PDF print out file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkPDF', baseContext);

            filePath = await boMerchandiseReturnsEditPage.downloadPDF(page);

            const exist = await utilsFile.doesFileExist(filePath);
            expect(exist, 'File does not exist').to.eq(true);
          });

          it('should check the file name', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkFileName', baseContext);

            const fileName = await boMerchandiseReturnsEditPage.getFileName(page);
            expect(fileName).to.eq('Print out');
          });

          it('should check the header of the return PDF', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnPDF', baseContext);

            let returnPrefix = '#RE00000';

            if (returnID >= 10) {
              returnPrefix = '#RE0000';
            }
            const isVisible = await utilsFile.isTextInPDF(filePath, `ORDER RETURN,,${todayDate},,${returnPrefix}${returnID}`);
            expect(isVisible, 'The header of the PDF is not correct!').to.eq(true);
          });

          it('should check the billing address in the PDF', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress', baseContext);

            const billingAddressExist = await utilsFile.isTextInPDF(
              filePath,
              'Billing & Delivery Address,,'
              + `${dataAddresses.address_2.firstName} ${dataAddresses.address_2.lastName},`
              + `${dataAddresses.address_2.company},`
              + `${dataAddresses.address_2.address},`
              + `${dataAddresses.address_2.secondAddress},`
              + `${dataAddresses.address_2.postalCode} ${dataAddresses.address_2.city},`
              + `${dataAddresses.address_2.country},`
              + `${dataAddresses.address_2.phone}`,
            );
            expect(billingAddressExist, 'Billing address is not correct in PDF!').to.eq(true);
          });

          it('should check the return number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkReturnNumber', baseContext);

            let returnPrefix = '00000';

            if (returnID >= 10) {
              returnPrefix = '0000';
            }

            const isVisible = await utilsFile.isTextInPDF(
              filePath,
              'We have logged your return request.,Your package must be returned to us within 14 days of receiving your order.'
              + `,,Return Number, ,Date,,${returnPrefix}${returnID}, ,${todayDate}`);
            expect(isVisible, 'Order return ID and the date are not correct!').to.eq(true);
          });

          it('should check the returned product details', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedProduct', baseContext);

            const isVisible = await utilsFile.isTextInPDF(
              filePath,
              `Items to be returned, ,Reference, ,Qty,,${dataProducts.demo_1.name} (Size: S - Color: White), ,`
              + `${dataProducts.demo_1.reference}, ,1`);
            expect(isVisible, 'Returned product details are not correct!').to.eq(true);
          });
        } else {
          it('should check that the file is not existing', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkFileNotExisting${index}`, baseContext);

            const fileName = await boMerchandiseReturnsEditPage.getFileName(page);
            expect(fileName).to.eq('--');
          });
        }
        it('should click on cancel button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `clickOnCancelButton${index}`, baseContext);

          await boMerchandiseReturnsEditPage.clickOnCancelButton(page);

          const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
        });

        it('should check the updated status in the merchandise returns table', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatus${index}`, baseContext);

          const status = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'name');
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
