// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import boMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';
import editMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns/edit';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {merchandiseReturnsPage as foMerchandiseReturnsPage} from '@pages/FO/classic/myAccount/merchandiseReturns';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';

// Import data
import OrderReturnStatuses from '@data/demo/orderReturnStatuses';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_merchandiseReturnsTab';

/*
Pre-condition:
- Create order in FO
- Enable merchandise returns
Scenario:
- Change order status to 'Shipped'
- Check 'Return products' button
- Create merchandise returns in FO then check it in BO
- Update status of merchandise return
- Waiting for package
- Package received
- Return complete
- Check the new status in BO and FO
Post-condition:
- Disable merchandise returns
 */
describe('BO - Orders - View and edit order : Check merchandise returns tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderID: number = 1;
  let returnID: string = '0';

  const merchandiseReturnsNumber: string = '#RE00000';
  const today: string = date.getDateFormat('mm/dd/yyyy');
  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe(`Change the new order status to '${dataOrderStatuses.shipped.name}'`, async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await ordersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await orderPageTabListBlock.isReturnProductsButtonVisible(page);
      expect(result).to.eq(true);
    });
  });

  describe('Create merchandise returns on FO', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Click on view my shop
      page = await orderPageTabListBlock.viewMyShop(page);
      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

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

      await orderDetailsPage.requestMerchandiseReturn(page, 'Test merchandise returns');

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await orderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });
  });

  describe('Check the existence of merchandise returns on \'Merchandise returns\' page', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should check the existence of the merchandise returns in the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistenceOfMerchandiseReturn', baseContext);

      await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID.toString());

      const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
      expect(result).to.contains(orderID);
    });

    it('should get the ID from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getTrackingNumber', baseContext);

      returnID = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order_return');
      expect(parseInt(returnID, 10)).to.not.equal(0);
    });
  });

  describe('Check the existence of the merchandise returns on \'Merchandise returns\' tab', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage2', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on \'Merchandise returns\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToMerchandiseReturnsTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should check that the merchandise returns number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

      const carriersNumber = await orderPageTabListBlock.getMerchandiseReturnsNumber(page);
      expect(carriersNumber).to.be.equal(1);
    });

    it('should check the merchandise returns details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierDetails1', baseContext);

      const result = await orderPageTabListBlock.getMerchandiseReturnsDetails(page);
      await Promise.all([
        expect(result.date).to.contains(today),
        expect(result.type).to.equal('Return'),
        expect(result.status).to.equal('Waiting for confirmation'),
        expect(result.number).to.equal(`${merchandiseReturnsNumber}${returnID}`),
      ]);
    });
  });

  [
    {args: {status: OrderReturnStatuses.waitingForPackage.name}},
    {args: {status: OrderReturnStatuses.packageReceived.name}},
    {args: {status: OrderReturnStatuses.returnCompleted.name}},
  ].forEach((test, index: number) => {
    describe(`Update status of merchandise return to '${test.args.status}'`, async () => {
      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.merchandiseReturnsLink,
        );
        await boMerchandiseReturnsPage.closeSfToolBar(page);

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should check the existence of the merchandise returns in the table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkExistenceOfReturns${index}`, baseContext);

        await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID.toString());

        const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
        expect(result).to.contains(orderID);
      });

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

    describe('Check the updated status of merchandise returns on view order page', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage0${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterOrder0${index}`, baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage0${index}`, baseContext);

        // View order
        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it('should click on \'Merchandise returns\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnMerchandiseReturn${index}`, baseContext);

        const isTabOpened = await orderPageTabListBlock.goToMerchandiseReturnsTab(page);
        expect(isTabOpened).to.eq(true);
      });

      it('should check that the merchandise returns number is equal to 1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMerchandiseReturnsNumber${index}`, baseContext);

        const carriersNumber = await orderPageTabListBlock.getMerchandiseReturnsNumber(page);
        expect(carriersNumber).to.be.equal(1);
      });

      it(`should check the merchandise returns status is '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCarrierDetails0${index}`, baseContext);

        const result = await orderPageTabListBlock.getMerchandiseReturnsDetails(page);
        expect(result.status).to.equal(test.args.status);
      });
    });

    describe('Check the updated status of merchandise returns on FO', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

        // Click on view my shop
        page = await orderPageTabListBlock.viewMyShop(page);
        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });

      it('should go to \'Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnPage${index}`, baseContext);

        await myAccountPage.goToMerchandiseReturnsPage(page);

        const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
      });

      it('should verify order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderReturnStatus${index}`, baseContext);

        const fileName = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
        expect(fileName).to.be.equal(test.args.status);
      });

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

        page = await orderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);
});
