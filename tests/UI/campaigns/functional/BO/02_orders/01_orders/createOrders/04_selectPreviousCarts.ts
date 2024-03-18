// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import stocksPage from '@pages/BO/catalog/stocks';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';
import viewShoppingCartPage from '@pages/BO/orders/shoppingCarts/view';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {productPage} from '@pages/FO/classic/product';

// Import data
import Carriers from '@data/demo/carriers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Frame, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_selectPreviousCarts';

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
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used to identify shopping cart page
  let shoppingCartPage: Frame|null;
  // Variable used to get the number of shopping carts
  let numberOfShoppingCarts: number;
  // Variable used to get the number of non ordered shopping carts
  let numberOfNonOrderedShoppingCarts: number;
  // Variable used to get the last shopping card ID
  let lastShoppingCartId: number = 0;
  // Variable used to get the available stock of the ordered product from BO > stocks page
  let availableStockOfOrderedProduct: number = 0;

  // Const used to get today date format
  const today: string = date.getDateFormat('yyyy-mm-dd');
  const todayCartFormat: string = date.getDateFormat('mm/dd/yyyy');
  // Const used for My carrier cost
  const myCarrierCost: number = 8.40;

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
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart1', baseContext);

      lastShoppingCartId = parseInt(
        await shoppingCartsPage.getTextColumn(page, 1, 'id_cart'),
        10,
      );
      expect(lastShoppingCartId).to.be.above(0);
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
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage1', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard1', baseContext);

      await addOrderPage.searchCustomer(page, dataCustomers.johnDoe.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      expect(customerName).to.contains(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton1', baseContext);

      const isBlockHistoryVisible = await addOrderPage.chooseCustomer(page);
      expect(isBlockHistoryVisible).to.eq(true);
    });
  });

  // 2 - Check that no records found on carts table
  describe('Check that no records found on carts table', async () => {
    it('should check that no records found in the carts section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundForCarts', baseContext);

      const noRecordsFoundText = await addOrderPage.getTextWhenCartsTableIsEmpty(page);
      expect(noRecordsFoundText).to.contains('No records found');
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
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst2', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts2', baseContext);

      await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists2', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts2', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart2', baseContext);

      lastShoppingCartId = parseInt(await shoppingCartsPage.getTextColumn(page, 1, 'id_cart'), 10);
      expect(lastShoppingCartId).to.be.above(0);
    });
  });

  // 3 - Create a Shopping cart from the FO by the default customer
  describe('Create a Shopping cart from the FO by the default customer', async () => {
    it('should click on view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewMyShop', baseContext);

      page = await addOrderPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
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

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should select My carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectMyCarrier', baseContext);

      const isPaymentStepDisplayed = await checkoutPage.chooseShippingMethodAndAddComment(
        page,
        Carriers.myCarrier.id,
      );
      expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.eq(true);
    });

    it('should close the current page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCurrentPage', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);

      await shoppingCartsPage.reloadPage(page);
      lastShoppingCartId = parseInt(await shoppingCartsPage.getTextColumn(page, 1, 'id_cart'), 10);
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
      expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it(`should filter by product '${Products.demo_1.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_1.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.at.least(1);
    });

    it('should get the Available stock of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      availableStockOfOrderedProduct = parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'), 10);
      expect(availableStockOfOrderedProduct).to.be.above(0);
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
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage2', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard2', baseContext);

      await addOrderPage.searchCustomer(page, dataCustomers.johnDoe.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      expect(customerName).to.contains(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton2', baseContext);

      const isBlockHistoryVisible = await addOrderPage.chooseCustomer(page);
      expect(isBlockHistoryVisible).to.eq(true);
    });

    it('should check the shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartId', baseContext);

      const cartId = await addOrderPage.getTextColumnFromCartsTable(page, 'id');
      expect(parseInt(cartId, 10)).to.be.equal(lastShoppingCartId);
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
          expect(cartColumn).to.contains(test.args.result);
        } else {
          expect(cartColumn).to.be.equal(test.args.result);
        }
      });
    });
  });

  // 6 - Check the Cart details
  describe('Check the Cart details', async () => {
    it('should click on details button and check the Cart Iframe is well displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailsButton', baseContext);

      const isIframeVisible = await addOrderPage.clickOnCartDetailsButton(page, 1);
      expect(isIframeVisible).to.eq(true);
    });

    it('should check the cart Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardId', baseContext);

      shoppingCartPage = addOrderPage.getShoppingCartIframe(page, lastShoppingCartId);
      expect(shoppingCartPage).to.not.eq(null);

      const cartId = await viewShoppingCartPage.getCartId(shoppingCartPage!);
      expect(cartId).to.be.equal(`Cart #${lastShoppingCartId}`);
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await viewShoppingCartPage.getCartTotal(shoppingCartPage!);
      expect(cartTotal.toString())
        .to.be.equal((Products.demo_1.finalPrice + myCarrierCost).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock', baseContext);

      const customerInformation = await viewShoppingCartPage
        .getCustomerInformation(shoppingCartPage!);
      expect(customerInformation)
        .to.contains(`${dataCustomers.johnDoe.socialTitle} ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`)
        .and.to.contains(dataCustomers.johnDoe.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the cart Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartInformationBlock', baseContext);

      const orderInformation = await viewShoppingCartPage.getOrderInformation(shoppingCartPage!);
      await expect(orderInformation).to.contains('The customer has not proceeded to checkout yet.');

      const hasButtonCreateOrderFromCart = await viewShoppingCartPage.hasButtonCreateOrderFromCart(shoppingCartPage!);
      expect(hasButtonCreateOrderFromCart).to.eq(true);
    });

    it('should check the product stock_available in cart Summary Block', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'checkProductStockAvailableInCartSummaryBlock', baseContext);

      const cartSummary = await viewShoppingCartPage.getTextColumn(shoppingCartPage!, 'stock_available');
      expect(cartSummary).to.contains(availableStockOfOrderedProduct.toString());
    });

    [
      {args: {columnName: 'image', result: Products.demo_1.thumbImage}},
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

        const cartSummary = await viewShoppingCartPage.getTextColumn(shoppingCartPage!, test.args.columnName);

        if (test.args.columnName === 'title') {
          expect(cartSummary).to.contains(test.args.result).and.to.contains(test.args.result_2);
        }
        expect(cartSummary).to.contains(test.args.result);
      });
    });
  });

  // 7 - Complete the order
  describe('Complete the order', async () => {
    it('should close the Shopping cart Iframe', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeIframe', baseContext);

      const isIframeNotVisible = await addOrderPage.closeIframe(page);
      expect(isIframeNotVisible, 'iframe shopping cart is still visible').to.eq(true);
    });

    it('should click on \'Use\' button and check that product table is visible on \'Cart\' block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUseButton', baseContext);

      const isProductTableVisible = await addOrderPage.clickOnCartUseButton(page);
      expect(isProductTableVisible, 'Product table is not visible!').to.eq(true);
    });

    it('should check the delivery option selected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySelected', baseContext);

      const deliveryOption = await addOrderPage.getDeliveryOption(page);
      expect(deliveryOption).to.contains(Carriers.myCarrier.name);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost', baseContext);

      const shippingCost = await addOrderPage.getShippingCost(page);
      expect(shippingCost).to.be.equal(`€${myCarrierCost.toFixed(2)}`);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const totalOrder = await addOrderPage.getTotal(page);
      expect(totalOrder).to.be.equal(`€${(Products.demo_1.finalPrice + myCarrierCost).toFixed(2)}`);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await addOrderPage.setSummaryAndCreateOrder(
        page,
        PaymentMethods.checkPayment.moduleName,
        OrderStatuses.paymentAccepted,
      );

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
    });
  });
});
