require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');
const {Products} = require('@data/demo/products');
const {Carriers} = require('@data/demo/carriers');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkMultiInvoice';

let browserContext;
let page;
let filePath;

let fileName = '';
const newProductprice = 35.50;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition :
- Create order by default customer
Scenario :

Post-condition

 */

describe('BO - Orders - View and edit order: Check multi invoice', async () => {
  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTable1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn, 'Lastname is not correct').to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Check multi invoice
  describe('Check multi invoice', async () => {
    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await viewOrderPage.updateOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should get the invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceNumber', baseContext);

      fileName = await viewOrderPage.getFileName(page);
      await expect(filePath).is.not.equal('');
    });

    it('should add the same ordered product and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct2', baseContext);

      await viewOrderPage.searchProduct(page, Products.demo_1.name);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.errorAddSameProductInInvoice(fileName));
    });

    it('should add the product to the cart and create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductWithNewInvoice', baseContext);

      await viewOrderPage.createNewInvoice(page);

      const carrierName = await viewOrderPage.getNewInvoiceCarrierName(page);
      await expect(carrierName).to.contains(`Carrier : ${Carriers.default.name}`);

      const isSelected = await viewOrderPage.isFreeShippingSelected(page);
      await expect(isSelected).to.be.false;

      await viewOrderPage.updateProductPrice(page, newProductprice);

      const textResult = await viewOrderPage.addProductToCart(page, 1, true);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check that order total price is correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay', baseContext);

      const totalPrice = await viewOrderPage.getOrderTotalPrice(page);
      await expect(totalPrice.toFixed(2)).to.equal((newProductprice * 2).toFixed(2));
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await viewOrderPage.reloadPage(page);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(2);
    });
  });
});
