require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const {getDateFormat} = require('@utils/date');

// Common tests BO
const loginCommon = require('@commonTests/BO/loginBO');
const {createCustomerTest, deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const {createAddressTest} = require('@commonTests/BO/customers/createDeleteAddress');

// Common tests FO
const {createShoppingCart} = require('@commonTests/FO/createShoppingCart');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const addOrderPage = require('@pages/BO/orders/add');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const shoppingCartViewPage = require('@pages/BO/orders/shoppingCarts/view');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');
const {Statuses} = require('@data/demo/orderStatuses');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_shoppingCarts_viewCarts';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfShoppingCarts;
let orderId;

const customerData = new CustomerFaker();
const orderData = {
  customer: customerData,
  product: Products.demo_1,
  productQuantity: 1,
};
const addressData = new AddressFaker({
  email: customerData.email,
  country: 'France',
});
const todayCartFormat = getDateFormat('mm/dd/yyyy');

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
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfShoppingCartsAfterFilter).to.be.at.equal(1);

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'c!lastname');
      await expect(textColumn).to.contains('Non ordered');
    });

    it('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage1', baseContext);

      await shoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await shoppingCartViewPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartViewPage.pageTitle);
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await shoppingCartViewPage.getCartTotal(page);
      await expect(cartTotal.toString())
        .to.be.equal((Products.demo_1.finalPrice).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock1', baseContext);

      const customerInformation = await shoppingCartViewPage.getCustomerInformation(page);
      await expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformationBlock1', baseContext);

      const orderInformation = await shoppingCartViewPage.getOrderInformation(page);
      await expect(orderInformation).to.contains('No order was created from this cart.');

      const hasButtonCreateOrderFromCart = await shoppingCartViewPage.hasButtonCreateOrderFromCart(page);
      await expect(hasButtonCreateOrderFromCart).to.be.true;
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbnailImage}},
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
          await expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        await expect(cartSummary).to.contains(test.args.result);
      });
    });

    it('should click on "Create an order from this cart." button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickCreateOrderFromCartButton', baseContext);

      await shoppingCartViewPage.createOrderFromThisCart(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should fill the order and create it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillAndCreateOrder', baseContext);

      // Choose payment method
      await addOrderPage.setPaymentMethod(page, PaymentMethods.checkPayment.moduleName);
      // Set order status
      await addOrderPage.setOrderStatus(page, Statuses.paymentAccepted);
      // Create the order
      await addOrderPage.clickOnCreateOrderButton(page);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);

      orderId = await orderPageProductsBlock.getOrderID(page);
      await expect(orderId).to.be.gt(0);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should search a shopping cart with a specific order Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSpecificOrderShoppingCarts', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', orderId);

      const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfShoppingCartsAfterFilter).to.be.at.equal(1);

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'status');
      await expect(textColumn).to.contains(orderId);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/29894
    it.skip('should go the Shopping Cart details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartDetailPage2', baseContext);

      await shoppingCartsPage.goToViewPage(page, 1);

      const pageTitle = await shoppingCartViewPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartViewPage.pageTitle);
    });

    it.skip('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock2', baseContext);

      const customerInformation = await shoppingCartViewPage.getCustomerInformation(page);
      await expect(customerInformation)
        .to.contains(`${customerData.socialTitle} ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(customerData.email)
        .and.to.contains(todayCartFormat);
    });

    it.skip('should check the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformationBlock2', baseContext);

      const orderInformation = await shoppingCartViewPage.getOrderInformation(page);
      await expect(orderInformation).to.contains(`Order #${orderId}`);

      const hasButtonCreateOrderFromCart = await shoppingCartViewPage.hasButtonCreateOrderFromCart(page);
      await expect(hasButtonCreateOrderFromCart).to.be.false;
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbnailImage}},
      {args: {columnName: 'title', result: Products.demo_1.name, result_2: Products.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cart', result: `€${(Products.demo_1.finalPrice).toFixed(2)}`, row: 0}},
    ].forEach((test) => {
      it.skip(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
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
          await expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        await expect(cartSummary).to.contains(test.args.result);
      });
    });

    it.skip('should click on the order Link in the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOrderLink', baseContext);

      await shoppingCartViewPage.goToOrderPage(page);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);

      const orderDetailId = await orderPageProductsBlock.getOrderID(page);
      await expect(orderDetailId).to.be.eq(orderId);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and check number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.above(0);
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
