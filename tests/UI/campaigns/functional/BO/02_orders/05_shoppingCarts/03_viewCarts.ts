// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import createShoppingCart from '@commonTests/FO/shoppingCart';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import addOrderPage from '@pages/BO/orders/add';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';
import shoppingCartViewPage from '@pages/BO/orders/shoppingCarts/view';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_shoppingCarts_viewCarts';

/*
  Pre-condition:
  - Create customer
  - Create address
  - Create a non-ordered shopping cart connected in the FO
  Scenario:
  - View Carts
  - Create Order from Cart
  - Check Order
  - Check Order link from the cart
  - Check Order
  Post-condition:
  - Delete customer
*/
describe('BO - Orders - Shopping carts : View carts', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShoppingCarts: number;
  let orderId: number;

  const customerData: CustomerData = new CustomerData();
  const orderData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
  });
  const addressData: AddressData = new AddressData({
    email: customerData.email,
    country: 'France',
  });
  const todayCartFormat: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest_1`);
  // Pre-condition: Create address
  createAddressTest(addressData, `${baseContext}_preTest_2`);
  // Pre-condition: Create a non-ordered shopping cart being connected in the FO
  createShoppingCart(orderData, `${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('View carts', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

      await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.equal(1);

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'status');
      expect(textColumn).to.contains('Non ordered');
    });

    it('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage1', baseContext);

      const lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await shoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await shoppingCartViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartViewPage.pageTitle(lastShoppingCartId));
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await shoppingCartViewPage.getCartTotal(page);
      expect(cartTotal.toString())
        .to.be.equal((Products.demo_1.finalPrice).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock1', baseContext);

      const customerInformation = await shoppingCartViewPage.getCustomerInformation(page);
      expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the cart Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartInformationBlock1', baseContext);

      const orderInformation = await shoppingCartViewPage.getOrderInformation(page);
      await expect(orderInformation).to.contains('The customer has not proceeded to checkout yet.');

      const hasButtonCreateOrderFromCart = await shoppingCartViewPage.hasButtonCreateOrderFromCart(page);
      expect(hasButtonCreateOrderFromCart).to.eq(true);
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbImage}},
      {args: {columnName: 'title', result: Products.demo_1.name, result_2: Products.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cart', result: `€${(Products.demo_1.finalPrice).toFixed(2)}`, row: 0}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(
            this,
            'testIdentifier',
            `checkProduct${test.args.columnName}InCartSummaryBlock1`,
            baseContext,
          );

        const cartSummary = await shoppingCartViewPage.getTextColumn(
          page,
          test.args.columnName,
          test.args.row === undefined ? 1 : test.args.row,
        );

        if (test.args.columnName === 'title') {
          expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        expect(cartSummary).to.contains(test.args.result);
      });
    });

    it('should click on "Create an order from this cart." button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickCreateOrderFromCartButton', baseContext);

      await shoppingCartViewPage.createOrderFromThisCart(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should fill the order and create it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillAndCreateOrder', baseContext);

      // Choose payment method
      await addOrderPage.setPaymentMethod(page, PaymentMethods.checkPayment.moduleName);
      // Set order status
      await addOrderPage.setOrderStatus(page, OrderStatuses.paymentAccepted);
      // Create the order
      await addOrderPage.clickOnCreateOrderButton(page);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);

      orderId = await orderPageProductsBlock.getOrderID(page);
      expect(orderId).to.be.gt(0);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should search a shopping cart with a specific order Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSpecificOrderShoppingCarts', baseContext);

      await shoppingCartsPage.resetFilter(page);
      await shoppingCartsPage.filterTable(page, 'input', 'id_order', orderId.toString());

      const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.be.at.equal(1);

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'id_order');
      expect(textColumn).to.contains(orderId);
    });

    it('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage2', baseContext);

      const lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await shoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await shoppingCartViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartViewPage.pageTitle(lastShoppingCartId));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock2', baseContext);

      const customerInformation = await shoppingCartViewPage.getCustomerInformation(page);
      expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformationBlock2', baseContext);

      const orderInformation = await shoppingCartViewPage.getOrderInformation(page);
      expect(orderInformation).to.contains(`Order #${orderId}`);

      const hasButtonCreateOrderFromCart = await shoppingCartViewPage.hasButtonCreateOrderFromCart(page);
      expect(hasButtonCreateOrderFromCart).to.eq(false);
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbImage}},
      {args: {columnName: 'title', result: Products.demo_1.name, result_2: Products.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cart', result: `€${(Products.demo_1.finalPrice).toFixed(2)}`, row: 0}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(
            this,
            'testIdentifier',
            `checkProduct${test.args.columnName}InCartSummaryBlock2`,
            baseContext,
          );

        const cartSummary = await shoppingCartViewPage.getTextColumn(
          page,
          test.args.columnName,
          test.args.row === undefined ? 1 : test.args.row,
        );

        if (test.args.columnName === 'title') {
          expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        expect(cartSummary).to.contains(test.args.result);
      });
    });

    it('should click on the order Link in the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOrderLink', baseContext);

      await shoppingCartViewPage.goToOrderPage(page);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);

      const orderDetailId = await orderPageProductsBlock.getOrderID(page);
      expect(orderDetailId).to.be.eq(orderId);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and check number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.above(0);
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
