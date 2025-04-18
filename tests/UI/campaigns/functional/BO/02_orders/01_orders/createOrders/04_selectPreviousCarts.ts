import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boOrdersViewBlockProductsPage,
  boShoppingCartsPage,
  boShoppingCartsViewPage,
  boStockPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Frame,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const todayCartFormat: string = utilsDate.getDateFormat('mm/dd/yyyy');
  // Const used for My carrier cost
  const myCarrierCost: number = 8.40;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Pre-condition: Delete non ordered shopping carts
  describe('PRE-TEST: Delete the Non ordered shopping carts', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await boShoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await boShoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await boShoppingCartsPage.bulkDeleteShoppingCarts(page);
        expect(deleteTextResult).to.be.contains(boShoppingCartsPage.successfulDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart1', baseContext);

      lastShoppingCartId = parseInt(
        await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart'),
        10,
      );
      expect(lastShoppingCartId).to.be.above(0);
    });
  });

  // 1 - Go to add new order page and choose default customer
  describe('Go to add new order page and choose default customer', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testidentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage1', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard1', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const customerName = await boOrdersCreatePage.getCustomerNameFromResult(page, 1);
      expect(customerName).to.contains(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton1', baseContext);

      const isBlockHistoryVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isBlockHistoryVisible).to.eq(true);
    });
  });

  // 2 - Check that no records found on carts table
  describe('Check that no records found on carts table', async () => {
    it('should check that no records found in the carts section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundForCarts', baseContext);

      const noRecordsFoundText = await boOrdersCreatePage.getTextWhenCartsTableIsEmpty(page);
      expect(noRecordsFoundText).to.contains('No records found');
    });
  });

  // 3 - Create a Shopping cart from the FO by the default customer
  describe('Create a Shopping cart from the FO by the default customer', async () => {
    it('should click on view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewMyShop', baseContext);

      page = await boOrdersCreatePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
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

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foClassicLoginPage.goToHomePage(page);
      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should select My carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectMyCarrier', baseContext);

      const isPaymentStepDisplayed = await foClassicCheckoutPage.chooseShippingMethodAndAddComment(
        page,
        dataCarriers.myCarrier.id,
      );
      expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.eq(true);
    });

    it('should close the current page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCurrentPage', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
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

      lastShoppingCartId = parseInt(await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart'), 10);
    });
  });

  // 4 - Get the Available stock of the ordered product
  describe('Get the Available stock of the ordered product', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.stocksLink,
      );

      const pageTitle = await boStockPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStockPage.pageTitle);
    });

    it(`should filter by product '${dataProducts.demo_1.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByProduct', baseContext);

      await boStockPage.simpleFilter(page, dataProducts.demo_1.name);

      const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.at.least(1);
    });

    it('should get the Available stock of the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      availableStockOfOrderedProduct = parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'available'), 10);
      expect(availableStockOfOrderedProduct).to.be.above(0);
    });
  });

  // 5 - Check the Carts table
  describe('Check the Carts table', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage2', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard2', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const customerName = await boOrdersCreatePage.getCustomerNameFromResult(page, 1);
      expect(customerName).to.contains(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton2', baseContext);

      const isBlockHistoryVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isBlockHistoryVisible).to.eq(true);
    });

    it('should check the shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartId', baseContext);

      const cartId = await boOrdersCreatePage.getTextColumnFromCartsTable(page, 'id');
      expect(parseInt(cartId, 10)).to.be.equal(lastShoppingCartId);
    });
    [
      {args: {columnName: 'date', result: today}},
      {args: {columnName: 'total', result: `€${(dataProducts.demo_1.finalPrice + myCarrierCost).toFixed(2)}`}},
    ].forEach((test) => {
      it(`should check the shopping cart ${test.args.columnName}`, async function () {
        await testContext
          .addContextItem(this, 'testIdentifier', `checkShoppingCart${test.args.columnName}`, baseContext);

        const cartColumn = await boOrdersCreatePage.getTextColumnFromCartsTable(page, test.args.columnName);

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

      const isIframeVisible = await boOrdersCreatePage.clickOnCartDetailsButton(page, 1);
      expect(isIframeVisible).to.eq(true);
    });

    it('should check the cart Id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardId', baseContext);

      shoppingCartPage = boOrdersCreatePage.getShoppingCartIframe(page, lastShoppingCartId);
      expect(shoppingCartPage).to.not.eq(null);

      const cartId = await boShoppingCartsViewPage.getCartId(shoppingCartPage!);
      expect(cartId).to.be.equal(`Cart #${lastShoppingCartId}`);
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCardTotal', baseContext);

      const cartTotal = await boShoppingCartsViewPage.getCartTotal(shoppingCartPage!);
      expect(cartTotal.toString())
        .to.be.equal((dataProducts.demo_1.finalPrice + myCarrierCost).toFixed(2));
    });

    it('should check the customer Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInformationBlock', baseContext);

      const customerInformation = await boShoppingCartsViewPage
        .getCustomerInformation(shoppingCartPage!);
      expect(customerInformation)
        .to.contains(`${dataCustomers.johnDoe.socialTitle} ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`)
        .and.to.contains(dataCustomers.johnDoe.email)
        .and.to.contains(todayCartFormat);
    });

    it('should check the cart Information Block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartInformationBlock', baseContext);

      const orderInformation = await boShoppingCartsViewPage.getOrderInformation(shoppingCartPage!);
      expect(orderInformation).to.contains('The customer has not proceeded to checkout yet.');

      const hasButtonCreateOrderFromCart = await boShoppingCartsViewPage.hasButtonCreateOrderFromCart(shoppingCartPage!);
      expect(hasButtonCreateOrderFromCart).to.eq(true);
    });

    it('should check the product stock_available in cart Summary Block', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'checkProductStockAvailableInCartSummaryBlock', baseContext);

      const cartSummary = await boShoppingCartsViewPage.getTextColumn(shoppingCartPage!, 'stock_available');
      expect(cartSummary).to.contains(availableStockOfOrderedProduct.toString());
    });

    [
      {args: {columnName: 'image', result: dataProducts.demo_1.thumbImage}},
      {args: {columnName: 'title', result: dataProducts.demo_1.name, result_2: dataProducts.demo_1.reference}},
      {args: {columnName: 'unit_price', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'quantity', result: 1}},
      {args: {columnName: 'total', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_products', result: `€${dataProducts.demo_1.finalPrice}`}},
      {args: {columnName: 'total_cost_shipping', result: `€${myCarrierCost}`}},
      {args: {columnName: 'total_cart', result: `€${(dataProducts.demo_1.finalPrice + myCarrierCost).toFixed(2)}`}},
    ].forEach((test) => {
      it(`should check the product's ${test.args.columnName} in cart Summary Block`, async function () {
        await testContext
          .addContextItem(this, 'testIdentifier', `checkProduct${test.args.columnName}InCartSummaryBlock`, baseContext);

        const cartSummary = await boShoppingCartsViewPage.getTextColumn(shoppingCartPage!, test.args.columnName);

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

      const isIframeNotVisible = await boOrdersCreatePage.closeIframe(page);
      expect(isIframeNotVisible, 'iframe shopping cart is still visible').to.eq(true);
    });

    it('should click on \'Use\' button and check that product table is visible on \'Cart\' block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUseButton', baseContext);

      const isProductTableVisible = await boOrdersCreatePage.clickOnCartUseButton(page);
      expect(isProductTableVisible, 'Product table is not visible!').to.eq(true);
    });

    it('should check the delivery option selected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySelected', baseContext);

      const deliveryOption = await boOrdersCreatePage.getDeliveryOption(page);
      expect(deliveryOption).to.contains(dataCarriers.myCarrier.name);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost', baseContext);

      const shippingCost = await boOrdersCreatePage.getShippingCost(page);
      expect(shippingCost).to.be.equal(`€${myCarrierCost.toFixed(2)}`);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const totalOrder = await boOrdersCreatePage.getTotal(page);
      expect(totalOrder).to.be.equal(`€${(dataProducts.demo_1.finalPrice + myCarrierCost).toFixed(2)}`);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await boOrdersCreatePage.setSummaryAndCreateOrder(
        page,
        dataPaymentMethods.checkPayment.moduleName,
        dataOrderStatuses.paymentAccepted,
      );

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });
  });
});
