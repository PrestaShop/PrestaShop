require('module-alias/register');

// Import Utils
const helper = require('@utils/helpers');
const {getDateFormat} = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {Carriers} = require('@data/demo/carriers');
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousCarts';

// Import expect rom chai
const {expect} = require('chai');


let browserContext;
let page;
let numberOfShoppingCarts;
let numberOfNonOrderedShoppingCarts;
let lastShoppingCartId;
const today = getDateFormat('yyyy-mm-dd');
const myCarrierCost = 8.40;

/*
Go to create Order page
Search and choose a customer
Select Previous Cart
 */

describe('BO - Orders : Create Order - Select Previous Carts', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async function () {
    browserContext = await helper.closeBrowserContext(this.browser);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

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
        // when we have a shopping cart non ordered (checkbox exists),
        // the column_id became column_id+1 = column of the lastname
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

  describe('Check that no records found of carts on create order BO for the default customer', async () => {
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

    it('should check that no records found in the carts section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundForCarts', baseContext);

      const noRecordsFoundText = await addOrderPage.getTextWhenCartsTableIsEmpty(page);
      await expect(noRecordsFoundText).to.contains('No records found');
    });
  });

  // todelete when this issue: https://github.com/PrestaShop/PrestaShop/issues/9589 is fixed
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
        // when we have a shopping cart non ordered (checkbox exists),
        // the column_id became column_id+1 = column of the lastname
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

  describe('Create a Shopping cart from the FO by the default customer', async () => {
    it('should click on view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewMyShop', baseContext);

      // The page will be changed when clicking on View my shop
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

      // Tab1 = 0 BO and Tab2 = 1 FO and we will focus on the BO page
      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });
  });

  describe('Check that the Carts table is not empty', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testidentifier', 'goToOrdersPage2', baseContext);

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
      await expect(parseInt(cartId, 10)).to.be.above(parseInt(lastShoppingCartId, 10));
    });

    it('should check the shopping cart date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartDate', baseContext);

      const cartDate = await addOrderPage.getTextColumnFromCartsTable(page, 'date');
      await expect(cartDate).to.contains(today);
    });

    it('should check the shopping cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartTotal', baseContext);

      const cartTotal = await addOrderPage.getTextColumnFromCartsTable(page, 'total');
      await expect(cartTotal)
        .to.be.equal(`€${(Products.demo_1.finalPrice + myCarrierCost).toFixed(2)}`);
    });
  });
});
