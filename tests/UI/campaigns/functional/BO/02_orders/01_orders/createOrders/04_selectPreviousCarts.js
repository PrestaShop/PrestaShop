require('module-alias/register');

const {expect} = require('chai');

// Import Utils
const helper = require('@utils/helpers');
const {getDateFormat} = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const viewShoppingCartPage = require('@pages/BO/orders/shoppingCarts/view');
const stocksPage = require('@pages/BO/catalog/stocks');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Carriers} = require('@data/demo/carriers');
const {Products} = require('@data/demo/products');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousCarts';

let browserContext;
let page;

// Variable used to identify shopping cart page
let shoppingCartPage;
// Variable used to get the number of shopping carts
let numberOfShoppingCarts;
// Variable used to get the number of non ordered shopping carts
let numberOfNonOrderedShoppingCarts;
// Variable used to get the last shopping card ID
let lastShoppingCartId = 0;
// Const used to get today date format
const today = getDateFormat('yyyy-mm-dd');
const todayCartFormat = getDateFormat('mm/dd/yyyy');
// Const used for My carrier cost
const myCarrierCost = 8.40;
// Variable used to get the available stock of the ordered product from BO > stocks page
let availableStockOfOrderedProduct = 0;
// Const used for the payment status
const paymentMethod = 'Payments by check';

/*
Pre-condition:
- Delete Non ordered carts from shopping cart page
Scenario:
- Create a cart from the FO by default customer
- Get the cart ID from Shopping cart page
- Get the stock available of the ordered Product
- Go to create Order page
- Search and choose a customer
- Check carts table
- Complete the order from a cart
 */

describe('BO - Orders - Create Order : Select Previous Carts', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    browserContext = await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Pre-condition: Delete non ordered shopping carts
  describe('PRE-TEST: Delete the Non ordered shopping carts', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'c!lastname');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart1', baseContext);

      lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await expect(parseInt(lastShoppingCartId, 10)).to.be.above(0);
    });
  });

  // 1 - Go to add new order page and choose default customer
  describe('Go to add new order page and choose default customer', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testidentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage1', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard1', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      await expect(customerName).to.contains(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton1', baseContext);

      const isBlockHistoryVisible = await addOrderPage.chooseCustomer(page);
      await expect(isBlockHistoryVisible).to.be.true;
    });
  });

  // 2 - Check that no records found on carts table
  describe('Check that no records found on carts table', async () => {
    it('should check that no records found in the carts section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundForCarts', baseContext);

      const noRecordsFoundText = await addOrderPage.getTextWhenCartsTableIsEmpty(page);
      await expect(noRecordsFoundText).to.contains('No records found');
    });
  });

  // To delete when this issue: https://github.com/PrestaShop/PrestaShop/issues/9589 is fixed
  describe('Delete the Non ordered shopping carts', async () => {
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

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst2', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts2', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'c!lastname');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists2', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts2', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart2', baseContext);

      lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await expect(parseInt(lastShoppingCartId, 10)).to.be.above(0);
    });
  });

  // 3 - Create a Shopping cart from the FO by the default customer
  describe('Create a Shopping cart from the FO by the default customer', async () => {
    it('should click on view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewMyShop', baseContext);

      page = await addOrderPage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should select My carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectMyCarrier', baseContext);

      const isPaymentStepDisplayed = await checkoutPage.chooseShippingMethodAndAddComment(
        page,
        Carriers.myCarrier.id = 2,
      );
      await expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.be.true;
    });

    it('should close the current page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCurrentPage', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);

      await shoppingCartsPage.reloadPage(page);
      lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'status');
    });
  });

  // 4 - Get the Available stock of the ordered product
  describe('Get the Available stock of the ordered product', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );

      const pageTitle = await stocksPage.getPageTitle(page);
      await expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it(`should filter by product '${Products.demo_1.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_1.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.be.at.least(1);
    });

    it('should get the Available stock of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      availableStockOfOrderedProduct = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
      await expect(availableStockOfOrderedProduct).to.be.above(0);
    });
  });

  // 5 - Check the Carts table
  describe('Check the Carts table', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage2', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard2', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      await expect(customerName).to.contains(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton2', baseContext);

      const isBlockHistoryVisible = await addOrderPage.chooseCustomer(page);
      await expect(isBlockHistoryVisible).to.be.true;
    });

    it('should check the shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartId', baseContext);

      const cartId = await addOrderPage.getTextColumnFromCartsTable(page, 'id');
      await expect(parseInt(cartId, 10)).to.be.equal(parseInt(lastShoppingCartId, 10));
    });
    [
      {args: {columnName: 'date', result: today}},
      {args: {columnName: 'total', result: `€${(Products.demo_1.finalPrice + myCarrierCost).toFixed(2)}`}},
    ].forEach((test) => {
      it(`should check the shopping cart ${test.args.columnName}`, async function () {
        await testContext
          .addContextItem(this, 'testIdentifier', `checkShoppingCart${test.args.columnName}`, baseContext);

        const cartColumn = await addOrderPage.getTextColumnFromCartsTable(page, test.args.columnName);

        if (test.args.columnName === 'date') {
          await expect(cartColumn).to.contains(test.args.result);
        } else {
          await expect(cartColumn).to.be.equal(test.args.result);
        }
      });
    });
  });

  // 6 - Check the Cart details
  describe('Check the Cart details', async () => {
    it('should click on details button and check the Cart Iframe is well displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailsButton', baseContext);

      const isIframeVisible = await addOrderPage.clickOnCartDetailsButton(page, 1);
      await expect(isIframeVisible).to.be.true;
    });

    it('should check the cart Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardId', baseContext);

      shoppingCartPage = addOrderPage.getShoppingCartIframe(page, lastShoppingCartId);
      const cartId = await viewShoppingCartPage.getCartId(shoppingCartPage);
      await expect(cartId).to.be.equal(`Cart #${lastShoppingCartId}`);
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await viewShoppingCartPage.getCartTotal(shoppingCartPage);
      await expect(cartTotal.toString())
        .to.be.equal((Products.demo_1.finalPrice + myCarrierCost).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock', baseContext);

      const customerInformation = await viewShoppingCartPage
        .getCustomerInformation(shoppingCartPage);
      await expect(customerInformation)
        .to.contains(`${DefaultCustomer.socialTitle} ${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`)
        .and.to.contains(DefaultCustomer.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the order Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformationBlock', baseContext);

      const orderInformation = await viewShoppingCartPage.getOrderInformation(shoppingCartPage);
      await expect(orderInformation)
        .to.contains('No order was created from this cart.')
        .and.to.contains('Create an order from this cart.');
    });

    it('should check the product stock_available in cart Summary Block', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'checkProductStockAvailableInCartSummaryBlock', baseContext);

      const cartSummary = await viewShoppingCartPage.getTextColumn(shoppingCartPage, 'stock_available');
      await expect(cartSummary).to.contains(availableStockOfOrderedProduct.toString());
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbnailImage}},
      {args: {columnName: 'title', result: Products.demo_1.name, result_2: Products.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${Products.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_shipping', result: `€${myCarrierCost}`}},
      {args: {columnName: 'total_cart', result: `€${(Products.demo_1.finalPrice + myCarrierCost).toFixed(2)}`}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(this, 'testIdentifier', `checkProduct${test.args.columnName}InCartSummaryBlock`, baseContext);

        const cartSummary = await viewShoppingCartPage.getTextColumn(shoppingCartPage, test.args.columnName);

        if (test.args.columnName === 'title') {
          await expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        await expect(cartSummary).to.contains(test.args.result);
      });
    });
  });

  // 7 - Complete the order
  describe('Complete the order', async () => {
    it('should close the Shopping cart Iframe', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeIframe', baseContext);

      const isIframeNotVisible = await addOrderPage.closeIframe(page);
      await expect(isIframeNotVisible, 'iframe shopping cart is still visible').to.be.true;
    });

    it('should click on \'Use\' button and check that product table is visible on \'Cart\' block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUseButton', baseContext);

      const isProductTableVisible = await addOrderPage.clickOnCartUseButton(page);
      await expect(isProductTableVisible, 'Product table is not visible!').to.be.true;
    });

    it('should check the delivery option selected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySelected', baseContext);

      const deliveryOption = await addOrderPage.getDeliveryOption(page);
      await expect(deliveryOption).to.contains(Carriers.myCarrier.name);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost', baseContext);

      const shippingCost = await addOrderPage.getShippingCost(page);
      await expect(shippingCost).to.be.equal(`€${myCarrierCost.toFixed(2)}`);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const totalOrder = await addOrderPage.getTotal(page);
      await expect(totalOrder).to.be.equal(`€${(Products.demo_1.finalPrice + myCarrierCost).toFixed(2)}`);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await addOrderPage.setSummaryAndCreateOrder(page, paymentMethod, Statuses.paymentAccepted);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
    });
  });
});
