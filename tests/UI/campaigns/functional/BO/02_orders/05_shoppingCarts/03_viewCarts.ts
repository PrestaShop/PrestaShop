import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import createShoppingCart from '@commonTests/FO/classic/shoppingCart';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersCreatePage,
  boOrdersViewBlockProductsPage,
  boShoppingCartsPage,
  boShoppingCartsViewPage,
  type BrowserContext,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const customerData: FakerCustomer = new FakerCustomer();
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
  });
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const todayCartFormat: string = utilsDate.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest_1`);
  // Pre-condition: Create address
  createAddressTest(addressData, `${baseContext}_preTest_2`);
  // Pre-condition: Create a non-ordered shopping cart being connected in the FO
  createShoppingCart(orderData, `${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('View carts', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

      await boShoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.equal(1);

      const textColumn = await boShoppingCartsPage.getTextColumn(page, 1, 'status');
      expect(textColumn).to.contains('Non ordered');
    });

    it('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage1', baseContext);

      const lastShoppingCartId = await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await boShoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await boShoppingCartsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsViewPage.pageTitle(lastShoppingCartId));
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await boShoppingCartsViewPage.getCartTotal(page);
      expect(cartTotal.toString())
        .to.be.equal((dataProducts.demo_1.finalPrice).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock1', baseContext);

      const customerInformation = await boShoppingCartsViewPage.getCustomerInformation(page);
      expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the cart Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartInformationBlock1', baseContext);

      const orderInformation = await boShoppingCartsViewPage.getOrderInformation(page);
      expect(orderInformation).to.contains('The customer has not proceeded to checkout yet.');

      const hasButtonCreateOrderFromCart = await boShoppingCartsViewPage.hasButtonCreateOrderFromCart(page);
      expect(hasButtonCreateOrderFromCart).to.eq(true);
    });

    [
      {args: {columnName: 'image', result: dataProducts.demo_1.thumbImage}},
      {args: {columnName: 'title', result: dataProducts.demo_1.name, result_2: dataProducts.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cart', result: `€${(dataProducts.demo_1.finalPrice).toFixed(2)}`, row: 0}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(
            this,
            'testIdentifier',
            `checkProduct${test.args.columnName}InCartSummaryBlock1`,
            baseContext,
          );

        const cartSummary = await boShoppingCartsViewPage.getTextColumn(
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

      await boShoppingCartsViewPage.createOrderFromThisCart(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it('should fill the order and create it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillAndCreateOrder', baseContext);

      // Choose payment method
      await boOrdersCreatePage.setPaymentMethod(page, dataPaymentMethods.checkPayment.moduleName);
      // Set order status
      await boOrdersCreatePage.setOrderStatus(page, dataOrderStatuses.paymentAccepted);
      // Create the order
      await boOrdersCreatePage.clickOnCreateOrderButton(page);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contain(boOrdersViewBlockProductsPage.pageTitle);

      orderId = await boOrdersViewBlockProductsPage.getOrderID(page);
      expect(orderId).to.be.gt(0);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should search a shopping cart with a specific order Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSpecificOrderShoppingCarts', baseContext);

      await boShoppingCartsPage.resetFilter(page);
      await boShoppingCartsPage.filterTable(page, 'input', 'id_order', orderId.toString());

      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.be.at.equal(1);

      const textColumn = await boShoppingCartsPage.getTextColumn(page, 1, 'id_order');
      expect(textColumn).to.contains(orderId);
    });

    it('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage2', baseContext);

      const lastShoppingCartId = await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await boShoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await boShoppingCartsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsViewPage.pageTitle(lastShoppingCartId));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock2', baseContext);

      const customerInformation = await boShoppingCartsViewPage.getCustomerInformation(page);
      expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformationBlock2', baseContext);

      const orderInformation = await boShoppingCartsViewPage.getOrderInformation(page);
      expect(orderInformation).to.contains(`Order #${orderId}`);

      const hasButtonCreateOrderFromCart = await boShoppingCartsViewPage.hasButtonCreateOrderFromCart(page);
      expect(hasButtonCreateOrderFromCart).to.eq(false);
    });

    [
      {args: {columnName: 'image', result: dataProducts.demo_1.thumbImage}},
      {args: {columnName: 'title', result: dataProducts.demo_1.name, result_2: dataProducts.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cart', result: `€${(dataProducts.demo_1.finalPrice).toFixed(2)}`, row: 0}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(
            this,
            'testIdentifier',
            `checkProduct${test.args.columnName}InCartSummaryBlock2`,
            baseContext,
          );

        const cartSummary = await boShoppingCartsViewPage.getTextColumn(
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

      await boShoppingCartsViewPage.goToOrderPage(page);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contain(boOrdersViewBlockProductsPage.pageTitle);

      const orderDetailId = await boOrdersViewBlockProductsPage.getOrderID(page);
      expect(orderDetailId).to.be.eq(orderId);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should reset all filters and check number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.above(0);
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
