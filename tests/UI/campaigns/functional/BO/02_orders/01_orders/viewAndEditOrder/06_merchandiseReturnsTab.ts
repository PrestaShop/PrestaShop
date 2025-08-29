// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  boMerchandiseReturnsEditPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
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
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  const today: string = utilsDate.getDateFormat('mm/dd/yyyy');
  // New order by customer data
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

  // Pre-condition: Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

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

  describe(`Change the new order status to '${dataOrderStatuses.shipped.name}'`, async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder1', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await boOrdersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await boOrdersViewBlockTabListPage.isReturnProductsButtonVisible(page);
      expect(result).to.eq(true);
    });
  });

  describe('Create merchandise returns on FO', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Click on view my shop
      page = await boOrdersViewBlockTabListPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

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

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'Test merchandise returns');

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });
  });

  describe('Check the existence of merchandise returns on \'Merchandise returns\' page', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
      );
      await boOrdersPage.closeSfToolBar(page);

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

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage2', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on \'Merchandise returns\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToMerchandiseReturnsTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should check that the merchandise returns number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

      const carriersNumber = await boOrdersViewBlockTabListPage.getMerchandiseReturnsNumber(page);
      expect(carriersNumber).to.be.equal(1);
    });

    it('should check the merchandise returns details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierDetails1', baseContext);

      const result = await boOrdersViewBlockTabListPage.getMerchandiseReturnsDetails(page);
      await Promise.all([
        expect(result.date).to.contains(today),
        expect(result.type).to.equal('Return'),
        expect(result.status).to.equal('Waiting for confirmation'),
        expect(result.number).to.equal(`${merchandiseReturnsNumber}${returnID}`),
      ]);
    });
  });

  [
    {args: {status: dataOrderReturnStatuses.waitingForPackage.name}},
    {args: {status: dataOrderReturnStatuses.packageReceived.name}},
    {args: {status: dataOrderReturnStatuses.returnCompleted.name}},
  ].forEach((test, index: number) => {
    describe(`Update status of merchandise return to '${test.args.status}'`, async () => {
      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customerServiceParentLink,
          boDashboardPage.merchandiseReturnsLink,
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

        const pageTitle = await boMerchandiseReturnsEditPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsEditPage.pageTitle);
      });

      it('should edit merchandise returns status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `editReturnStatus${index}`, baseContext);

        const textResult = await boMerchandiseReturnsEditPage.setStatus(page, test.args.status);
        expect(textResult).to.contains(boMerchandiseReturnsEditPage.successfulUpdateMessage);
      });
    });

    describe('Check the updated status of merchandise returns on view order page', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage0${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterOrder0${index}`, baseContext);

        await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

        const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
        expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage0${index}`, baseContext);

        // View order
        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it('should click on \'Merchandise returns\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnMerchandiseReturn${index}`, baseContext);

        const isTabOpened = await boOrdersViewBlockTabListPage.goToMerchandiseReturnsTab(page);
        expect(isTabOpened).to.eq(true);
      });

      it('should check that the merchandise returns number is equal to 1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMerchandiseReturnsNumber${index}`, baseContext);

        const carriersNumber = await boOrdersViewBlockTabListPage.getMerchandiseReturnsNumber(page);
        expect(carriersNumber).to.be.equal(1);
      });

      it(`should check the merchandise returns status is '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCarrierDetails0${index}`, baseContext);

        const result = await boOrdersViewBlockTabListPage.getMerchandiseReturnsDetails(page);
        expect(result.status).to.equal(test.args.status);
      });
    });

    describe('Check the updated status of merchandise returns on FO', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

        // Click on view my shop
        page = await boOrdersViewBlockTabListPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
      });

      it('should go to \'Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnPage${index}`, baseContext);

        await foClassicMyAccountPage.goToMerchandiseReturnsPage(page);

        const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
      });

      it('should verify order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderReturnStatus${index}`, baseContext);

        const fileName = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'status');
        expect(fileName).to.be.equal(test.args.status);
      });

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

        page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);
});
