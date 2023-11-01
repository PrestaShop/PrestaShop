// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';
import date from '@utils/date';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import merchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
import editMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns/edit';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';
import foMerchandiseReturnsPage from '@pages/FO/myAccount/merchandiseReturns';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';
import {orderHistoryPage} from '@pages/FO/myAccount/orderHistory';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderReturnStatuses from '@data/demo/orderReturnStatuses';
import Addresses from '@data/demo/address';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customerService_merchandiseReturns_updateStatus';

/*
Pre-condition:
- Create order in FO
- Activate merchandise returns
- Change the first order status in the list to shipped
Scenario
- Create merchandise returns in FO
- GO to BO > merchandise returns page > Edit
- Test all return statuses
Post-condition:
- Deactivate merchandise returns
 */
describe('BO - Customer Service - Merchandise Returns : Update status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;
  let returnID: number;
  const todayDate: string = date.getDateFormat('mm/dd/yyyy');

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foLoginPage.goToHomePage(page);

      // Add first product to cart by quick view
      await homePage.addProductToCartByQuickView(page, 1, 2);
      await homePage.proceedToCheckout(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(2);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('PRE-TEST: Enable merchandise returns', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );
      await merchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, true);
      expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });

  describe('PRE-TEST: Change order status to \'Shipped\'', async () => {
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

  describe('POST-TEST: Disable merchandise returns', async () => {
    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, false);
      expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
});
