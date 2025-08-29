import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
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
  foClassicMyReturnDetailsPage,
  type Page,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_merchandiseReturns_checkOrderReturnPDF';

/*
Pre-condition:
- Create new order by default customer
- Enable merchandise returns
Scenario
- Create merchandise returns
- Change return status to waiting for package
- Check merchandise returns return PDF
Post-condition:
- Disable merchandise returns
 */
describe('FO - Account : Check order return PDF', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderID: number;
  let orderReference: string;
  let orderDate: string;
  let filePath: string|null;
  let fileName: string = '#RE0000';

  const today: string = utilsDate.getDateFormat('mm/dd/yyyy');
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

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBasePage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      // Click on view my shop
      page = await boOrdersViewBasePage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logonFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

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

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'message test');

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });
  });

  describe('Check merchandise returns', async () => {
    it('should verify the Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnReference', baseContext);

      const packageStatus = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'orderReference');
      expect(packageStatus).to.equal(orderReference);
    });

    it('should verify the Order return file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnFileName', baseContext);

      const packageStatus = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'fileName');
      expect(packageStatus).to.contains('#RE00');
    });

    it('should verify the order return status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus1', baseContext);

      const packageStatus = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'status');
      expect(packageStatus).to.equal(dataOrderReturnStatuses.waitingForConfirmation.name);
    });

    it('should verify the order return date issued', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnDateIssued', baseContext);

      const packageStatus = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'dateIssued');
      expect(packageStatus).to.equal(orderDate);
    });

    it('should go to return details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToReturnDetails', baseContext);

      await foClassicMyMerchandiseReturnsPage.goToReturnDetailsPage(page);

      const pageTitle = await foClassicMyReturnDetailsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyReturnDetailsPage.pageTitle);
    });

    it('should check the return notification', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnNotification', baseContext);

      const orderReturnNotifications = await foClassicMyReturnDetailsPage.getAlertWarning(page);
      expect(orderReturnNotifications).to.equal(foClassicMyReturnDetailsPage.errorMessage);
    });

    it('should check the return details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnDetails', baseContext);

      const orderReturnInfo = await foClassicMyReturnDetailsPage.getOrderReturnInfo(page);
      expect(orderReturnInfo)
        .to.contains(`on ${orderDate} ${foClassicMyReturnDetailsPage.orderReturnCardBlock}`)
        .and.to.contains(dataOrderReturnStatuses.waitingForConfirmation.name)
        .and.to.contains(`List of items to be returned: Product Quantity ${dataProducts.demo_1.name} `
          + `(Size: S - Color: White) Reference: ${dataProducts.demo_1.reference} 1`);
    });
  });

  describe('Check the return details PDF', async () => {
    describe(`Change the merchandise returns status to '${dataOrderReturnStatuses.waitingForPackage.name}'`, async () => {
      it('should go to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToBO', baseContext);

        await foClassicMyMerchandiseReturnsPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customerServiceParentLink,
          boDashboardPage.merchandiseReturnsLink,
        );

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should check the existence of the merchandise returns in the table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkExistenceOfReturns', baseContext);

        await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID.toString());

        const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
        expect(result).to.contains(orderID);
      });

      it('should get the return ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getReturnID', baseContext);

        const idReturn = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(
          page,
          'id_order_return',
        );
        expect(parseInt(idReturn, 10)).to.be.above(0);

        if (parseInt(idReturn, 10) >= 10) {
          fileName += idReturn;
        } else fileName += `0${idReturn}`;
      });

      it('should go to edit merchandise returns page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditReturnsPage', baseContext);

        await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

        const pageTitle = await boMerchandiseReturnsEditPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsEditPage.pageTitle);
      });

      it('should edit merchandise returns status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editReturnStatus', baseContext);

        const textResult = await boMerchandiseReturnsEditPage.setStatus(page, dataOrderReturnStatuses.waitingForPackage.name);
        expect(textResult).to.contains(boMerchandiseReturnsEditPage.successfulUpdateMessage);
      });
    });

    describe('Check merchandise return PDF', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

        // Click on view my shop
        page = await boMerchandiseReturnsEditPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
      });

      it('should go to \'Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnPage', baseContext);

        await foClassicMyAccountPage.goToMerchandiseReturnsPage(page);

        const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
      });

      it('should verify the order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus2', baseContext);

        const fileName = await foClassicMyMerchandiseReturnsPage.getTextColumn(page, 'status');
        expect(fileName).to.be.equal(dataOrderReturnStatuses.waitingForPackage.name);
      });

      it('should download the return form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadReturnForm', baseContext);

        filePath = await foClassicMyMerchandiseReturnsPage.downloadReturnForm(page, 1);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });

      it('should check the PDF Header ', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnFileName', baseContext);

        const isVisible = await utilsFile.isTextInPDF(filePath, `ORDER RETURN,,${today},,${fileName},,`);
        expect(isVisible, 'The order return file name is not correct!').to.eq(true);
      });

      it('should check the Billing & delivery address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress', baseContext);

        const isVisible = await utilsFile.isTextInPDF(filePath, `Billing & Delivery Address,,${dataCustomers.johnDoe.firstName}`
          + ` ${dataCustomers.johnDoe.lastName},${dataAddresses.address_2.company},${dataAddresses.address_2.address},`
          + `${dataAddresses.address_2.secondAddress},${dataAddresses.address_2.postalCode} ${dataAddresses.address_2.city}`
          + `,${dataAddresses.address_2.country},${dataAddresses.address_2.phone},,`);

        expect(isVisible, 'Billing and delivery address are not correct!').to.eq(true);
      });

      it('should check the number of returned days', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedDays', baseContext);

        const isVisible = await utilsFile.isTextInPDF(filePath, 'We have logged your return request.,Your package must '
          + 'be returned to us within 14 days of receiving your order.');
        expect(isVisible, 'returned days number is not correct!').to.eq(true);
      });

      it('should check the returned product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedProduct', baseContext);

        const isVisible = await utilsFile.isTextInPDF(filePath, 'Items to be returned, ,Reference, ,Qty,,'
          + `${dataProducts.demo_1.name} (Size: S - Color: White), ,${dataProducts.demo_1.reference}, ,1`);

        expect(isVisible, 'returned product list is not correct!').to.eq(true);
      });
    });
  });

  // Post-condition : Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);
});
