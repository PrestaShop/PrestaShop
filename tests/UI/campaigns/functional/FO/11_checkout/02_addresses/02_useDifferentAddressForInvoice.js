require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

// Create faker data
const guestData = new CustomerFaker({password: ''});
const deliveryAddress = new AddressFaker();
const invoiceAddress = new AddressFaker();


// Import test context
const testContext = require('@utils/testContext');

// context
const baseContext = 'functional_FO_checkout_addresses_useDifferentAddressForInvoice';

let browserContext;
let page;


/*
Go to FO
Add product to cart
Go to checkout page
Choose to order as guest
Add guest information
Add delivery address
Click on Use another address for invoice
Fill a second form address
Finish the order

Go to BO > orders page
Go to order view page
Check that the 2 addresses are different
*/

describe('FO - Guest checkout: Use different invoice address', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Make an order with 2 different addresses for delivery and invoice', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // Go to FO
      await foHomePage.goToFo(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 4);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page, foProductPage);

      const pageTitle = await foCartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await foCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      const isStepCompleted = await foCheckoutPage.setGuestPersonalInformation(page, guestData);
      await expect(isStepCompleted).to.be.true;
    });

    it('should fill different delivery and invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerAddresses', baseContext);

      const isStepCompleted = await foCheckoutPage.setAddress(page, deliveryAddress, invoiceAddress);
      await expect(isStepCompleted).to.be.true;
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeTheOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Go to BO and check that invoice address is different from delivery address', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to the orders page', async function () {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${guestData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.resetFilter(page);
      await ordersPage.filterOrders(page, 'input', 'customer', guestData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(guestData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check that invoice and delivery addresses are different', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesInViewOrder', baseContext);

      const finalDeliveryAddress = await viewOrderPage.getShippingAddress(page);
      const finalInvoiceAddress = await viewOrderPage.getInvoiceAddress(page);

      await expect(
        finalDeliveryAddress.replace('Shipping', ''),
        'Invoice and delivery addresses shouldn\'t be the same',
      )
        .to.not.equal(finalInvoiceAddress.replace('Invoice', ''));
    });
  });
});
