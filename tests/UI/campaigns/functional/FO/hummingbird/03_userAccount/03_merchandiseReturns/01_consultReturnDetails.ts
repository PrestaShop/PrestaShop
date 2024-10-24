// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import BO pages
import boMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';
import editMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns/edit';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
// Import FO pages
import foMerchandiseReturnsPage from '@pages/FO/hummingbird/myAccount/merchandiseReturns';
import orderDetailsPage from '@pages/FO/hummingbird/myAccount/orderDetails';
import orderHistoryPage from '@pages/FO/hummingbird/myAccount/orderHistory';
import returnDetailsPage from '@pages/FO/hummingbird/myAccount/returnDetails';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCustomers,
  dataOrderReturnStatuses,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_userAccount_merchandiseReturns_consultReturnDetails';

/*
Pre-condition:
- Install the theme hummingbird
- Create new order by default customer
- Enable merchandise returns
Scenario
- Create merchandise returns
- Check merchandise returns details with all status
Post-condition:
- Disable merchandise returns
- uninstall the theme hummingbird
 */
describe('FO - Account : Consult return details', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderID: number;
  let orderReference: string;
  let orderDate: string;
  let fileName: string = '#RE0000';

  // New order by customer data
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

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_0`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe(`Case 1 : Check merchandise returns status '${dataOrderReturnStatuses.waitingForConfirmation.name}'`, async () => {
    describe(`Change the created orders status to '${dataOrderStatuses.shipped.name}'`, async () => {
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

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

        await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

        const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
        expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
      });

      it('should get the order ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

        orderID = await boOrdersPage.getOrderIDNumber(page);
        expect(orderID).to.not.equal(1);
      });

      it('should get the created Order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

        orderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
        expect(orderReference).to.not.eq(null);
      });

      it('should get the created Order date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderDate', baseContext);

        orderDate = await boOrdersPage.getTextColumn(page, 'date_add', 1);
        orderDate = orderDate.substring(0, 10);
        expect(orderDate).to.not.eq(null);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

        // View order
        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderBasePage.getPageTitle(page);
        expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const result = await viewOrderBasePage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
        expect(result).to.equal(dataOrderStatuses.shipped.name);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

        const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
        expect(numberOfOrders).to.be.above(0);
      });
    });

    describe('Create merchandise returns', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        // Click on view my shop
        page = await viewOrderBasePage.viewMyShop(page);
        // Change FO language
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should login', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logonFO', baseContext);

        await foHummingbirdHomePage.goToLoginPage(page);
        await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

        const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected).to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'Order history and details\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

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

        await orderDetailsPage.requestMerchandiseReturn(page, 'message test');

        const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
      });
    });

    describe('Check merchandise returns', async () => {
      it('should verify the Order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnReference', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'orderReference');
        expect(packageStatus).to.equal(orderReference);
      });

      it('should verify the Order return file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnFileName', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'fileName');
        expect(packageStatus).to.contains('#RE00');
      });

      it('should verify the order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
        expect(packageStatus).to.equal(dataOrderReturnStatuses.waitingForConfirmation.name);
      });

      it('should verify the order return date issued', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnDateIssued', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'dateIssued');
        expect(packageStatus).to.equal(orderDate);
      });

      it('should go to return details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToReturnDetails', baseContext);

        await foMerchandiseReturnsPage.goToReturnDetailsPage(page);

        const pageTitle = await returnDetailsPage.getPageTitle(page);
        expect(pageTitle).to.contains(returnDetailsPage.pageTitle);
      });

      it('should check the return notification', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnNotification', baseContext);

        const orderReturnNotifications = await returnDetailsPage.getAlertWarning(page);
        expect(orderReturnNotifications).to.equal(returnDetailsPage.errorMessage);
      });

      it('should check the return details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnDetails', baseContext);

        const orderReturnInfo = await returnDetailsPage.getOrderReturnInfo(page);
        expect(orderReturnInfo)
          .to.contains(`on ${orderDate} ${returnDetailsPage.orderReturnCardBlock}`)
          .and.to.contains(dataOrderReturnStatuses.waitingForConfirmation.name)
          .and.to.contains(`List of items to be returned: Product Quantity ${dataProducts.demo_1.name} `
            + `(Size: S - Color: White) Reference: ${dataProducts.demo_1.reference} 1`);
      });
    });
  });

  const tests = [
    {args: {status: dataOrderReturnStatuses.waitingForPackage.name}},
    {args: {status: dataOrderReturnStatuses.packageReceived.name}},
    {args: {status: dataOrderReturnStatuses.returnDenied.name}},
    {args: {status: dataOrderReturnStatuses.returnCompleted.name}},
  ];
  tests.forEach((test, index: number) => {
    describe(`Case ${index + 2} : Check merchandise returns with the status ${test.args.status}`, async () => {
      describe('Change the merchandise returns status', async () => {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBO${index}`, baseContext);

          await foMerchandiseReturnsPage.goTo(page, global.BO.URL);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDashboardPage.pageTitle);
        });

        it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.customerServiceParentLink,
            boDashboardPage.merchandiseReturnsLink,
          );

          const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
        });

        it('should check the existence of the merchandise returns in the table', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkExistenceOfReturns${index}`, baseContext);

          await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID.toString());

          const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
          expect(result).to.contains(orderID);
        });

        if (index === 0) {
          it('should get the return ID', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'getReturnID', baseContext);

            const idReturn = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(
              page,
              'id_order_return',
            );
            expect(parseInt(idReturn, 10)).to.be.above(0);

            if (parseInt(idReturn, 10) > 10) {
              fileName += idReturn;
            } else fileName += `0${idReturn}`;
          });
        }

        it('should go to edit merchandise returns page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToEditReturnsPage${index}`, baseContext);

          await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

          const pageTitle = await editMerchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(editMerchandiseReturnsPage.pageTitle);
        });

        it('should edit merchandise returns status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `editReturnStatus${index}`, baseContext);

          const textResult = await editMerchandiseReturnsPage.setStatus(page, test.args.status);
          expect(textResult).to.contains(editMerchandiseReturnsPage.successfulUpdateMessage);
        });
      });

      describe('Check merchandise return details', async () => {
        it('should go to FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

          // Click on view my shop
          page = await editMerchandiseReturnsPage.viewMyShop(page);
          // Change FO language
          await foHummingbirdHomePage.changeLanguage(page, 'en');

          const isHomePage = await foHummingbirdHomePage.isHomePage(page);
          expect(isHomePage, 'Home page is not displayed').to.eq(true);
        });

        it('should go to account page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index + 3}`, baseContext);

          await foHummingbirdHomePage.goToMyAccountPage(page);

          const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
          expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
        });

        it('should go to \'Merchandise Returns\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnPage${index}`, baseContext);

          await foHummingbirdMyAccountPage.goToMerchandiseReturnsPage(page);

          const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
          expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
        });

        it('should verify the order return status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkOrderReturnStatus${index}`, baseContext);

          const fileName = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
          expect(fileName).to.be.equal(test.args.status);
        });

        it('should go to return details page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToReturnDetails${index}`, baseContext);

          await foMerchandiseReturnsPage.goToReturnDetailsPage(page);

          const pageTitle = await returnDetailsPage.getPageTitle(page);
          expect(pageTitle).to.contains(returnDetailsPage.pageTitle);
        });

        it('should check that the alert warning is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkReturnNotification${index}`, baseContext);

          const isVisible = await returnDetailsPage.isAlertWarningVisible(page);
          expect(isVisible).to.eq(false);
        });

        it('should check the return details', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkReturnDetails${index}`, baseContext);

          const orderReturnInfo = await returnDetailsPage.getOrderReturnInfo(page);
          expect(orderReturnInfo)
            .to.contains(`${fileName} on ${orderDate} ${returnDetailsPage.orderReturnCardBlock}`)
            .and.to.contains(test.args.status)
            .and.to.contains(`List of items to be returned: Product Quantity ${dataProducts.demo_1.name} `
              + `(Size: S - Color: White) Reference: ${dataProducts.demo_1.reference} 1`);
        });
      });
    });
  });

  // Post-condition : Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_2`);
});
