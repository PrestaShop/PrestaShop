require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');
const {
  enableMerchandiseReturns,
  disableMerchandiseReturns,
} = require('@commonTests/BO/customerService/enableDisableMerchandiseReturns');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');
const foMerchandiseReturnsPage = require('@pages/FO/myAccount/merchandiseReturns');
const returnDetailsPage = require('@pages/FO/myAccount/returnDetails');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const boMerchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view/viewOrderBasePage');
const editMerchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns/edit');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');
const {ReturnStatuses} = require('@data/demo/orderReturnStatuses');
const {Products} = require('@data/demo/products');
const Address = require('@data/demo/address');

const baseContext = 'functional_FO_userAccount_merchandiseReturns_checkOrderReturnPDF';

let browserContext;
let page;
let orderID;
let orderReference;
let orderDate;
let filePath;
let fileName = '#RE0000';
const today = getDateFormat('mm/dd/yyyy');

// New order by customer data
const orderData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

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
  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

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

  describe(`Change the created orders status to '${Statuses.shipped.status}'`, async () => {
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await ordersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should get the created Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

      orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
      await expect(orderReference).to.not.be.null;
    });

    it('should get the created Order date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderDate', baseContext);

      orderDate = await ordersPage.getTextColumn(page, 'date_add', 1);
      orderDate = orderDate.substr(0, 10);
      await expect(orderDate).to.not.be.null;
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });
  });

  describe('Create merchandise returns', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logonFO', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await orderHistoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      await orderHistoryPage.goToDetailsPage(page, 1);

      const result = await orderDetailsPage.isOrderReturnFormVisible(page);
      await expect(result).to.be.true;
    });

    it('should create a merchandise return', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await orderDetailsPage.requestMerchandiseReturn(page, 'message test');

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });
  });

  describe('Check merchandise returns', async () => {
    it('should verify the Order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnReference', baseContext);

      const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'orderReference');
      await expect(packageStatus).to.equal(orderReference);
    });

    it('should verify the Order return file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnFileName', baseContext);

      const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'fileName');
      await expect(packageStatus).to.contains('#RE00');
    });

    it('should verify the order return status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus1', baseContext);

      const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
      await expect(packageStatus).to.equal(ReturnStatuses.waitingForConfirmation.name);
    });

    it('should verify the order return date issued', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnDateIssued', baseContext);

      const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'dateIssued');
      await expect(packageStatus).to.equal(orderDate);
    });

    it('should go to return details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToReturnDetails', baseContext);

      await foMerchandiseReturnsPage.goToReturnDetailsPage(page);

      const pageTitle = await returnDetailsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(returnDetailsPage.pageTitle);
    });

    it('should check the return notification', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnNotification', baseContext);

      const orderReturnNotifications = await returnDetailsPage.getAlertWarning(page);
      await expect(orderReturnNotifications).to.equal(returnDetailsPage.errorMessage);
    });

    it('should check the return details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnDetails', baseContext);

      const orderReturnInfo = await returnDetailsPage.getOrderReturnInfo(page);
      await expect(orderReturnInfo)
        .to.contains(`on ${orderDate} ${returnDetailsPage.orderReturnCardBlock}`)
        .and.to.contains(ReturnStatuses.waitingForConfirmation.name)
        .and.to.contains(`List of items to be returned: Product Quantity ${Products.demo_1.name} `
          + `(Size: S - Color: White) Reference: ${Products.demo_1.reference} 1`);
    });
  });

  describe('Check the return details PDF', async () => {
    describe(`Change the merchandise returns status to '${ReturnStatuses.waitingForPackage.name}'`, async () => {
      it('should go to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToBO', baseContext);

        await foMerchandiseReturnsPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.merchandiseReturnsLink,
        );

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should check the existence of the merchandise returns in the table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkExistenceOfReturns', baseContext);

        await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID);

        const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
        await expect(result).to.contains(orderID);
      });

      it('should get the return ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getReturnID', baseContext);

        const idReturn = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(
          page,
          'id_order_return',
        );
        await expect(parseInt(idReturn, 10)).to.be.above(0);

        if (parseInt(idReturn, 10) >= 10) {
          fileName += idReturn;
        } else fileName += `0${idReturn}`;
      });

      it('should go to edit merchandise returns page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditReturnsPage', baseContext);

        await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

        const pageTitle = await editMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(editMerchandiseReturnsPage.pageTitle);
      });

      it('should edit merchandise returns status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editReturnStatus', baseContext);

        const textResult = await editMerchandiseReturnsPage.setStatus(page, ReturnStatuses.waitingForPackage.name);
        await expect(textResult).to.contains(editMerchandiseReturnsPage.successfulUpdateMessage);
      });
    });

    describe('Check merchandise return PDF', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

        // Click on view my shop
        page = await editMerchandiseReturnsPage.viewMyShop(page);

        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });

      it('should go to \'Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnPage', baseContext);

        await myAccountPage.goToMerchandiseReturnsPage(page);

        const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
      });

      it('should verify the order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus2', baseContext);

        const fileName = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
        await expect(fileName).to.be.equal(ReturnStatuses.waitingForPackage.name);
      });

      it('should download the return form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadReturnForm', baseContext);

        filePath = await foMerchandiseReturnsPage.downloadReturnForm(page, 1);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'PDF file was not downloaded').to.be.true;
      });

      it('should check the PDF Header ', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnFileName', baseContext);

        const isVisible = await files.isTextInPDF(filePath, `ORDER RETURN,,${today},,${fileName},,`);
        await expect(isVisible, 'The order return file name is not correct!').to.be.true;
      });

      it('should check the Billing & delivery address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress', baseContext);

        const isVisible = await files.isTextInPDF(filePath, `Billing & Delivery Address,,${DefaultCustomer.firstName}`
          + ` ${DefaultCustomer.lastName},${Address.second.company},${Address.second.address},`
          + `${Address.second.secondAddress},${Address.second.postalCode} ${Address.second.city}`
          + `,${Address.second.country},${Address.second.phone},, ,`);

        await expect(isVisible, 'Billing and delivery address are not correct!').to.be.true;
      });

      it('should check the number of returned days', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedDays', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'We have logged your return request.,Your package must '
          + 'be returned to us within 14 days of receiving your order.');
        await expect(isVisible, 'returned days number is not correct!').to.be.true;
      });

      it('should check the returned product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnedProduct', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Items to be returned, ,Reference, ,Qty,,'
          + `${Products.demo_1.name} (Size: S - Color: White), ,${Products.demo_1.reference}, ,1`);

        await expect(isVisible, 'returned product list is not correct!').to.be.true;
      });
    });
  });

  // Post-condition : Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);
});
