// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

// Import FO pages
import {homePage as foHomePage} from '@pages/FO/home';
import foProductPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import foCheckoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_addresses_useDifferentAddressForInvoice';

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
  // Create faker data
  const guestData: CustomerData = new CustomerData({password: ''});
  const deliveryAddress: AddressData = new AddressData({country: 'France'});
  const invoiceAddress: AddressData = new AddressData({country: 'France'});

  let browserContext: BrowserContext;
  let page: Page;
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
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 4);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page, 1);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should fill customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      const isStepCompleted = await foCheckoutPage.setGuestPersonalInformation(page, guestData);
      expect(isStepCompleted).to.eq(true);
    });

    it('should fill different delivery and invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerAddresses', baseContext);

      const isStepCompleted = await foCheckoutPage.setAddress(page, deliveryAddress, invoiceAddress);
      expect(isStepCompleted).to.eq(true);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeTheOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

      // Payment step - Choose payment step
      await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
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
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${guestData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.resetFilter(page);
      await ordersPage.filterOrders(page, 'input', 'customer', guestData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(guestData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check that invoice and delivery addresses are different', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesInViewOrder', baseContext);

      const finalDeliveryAddress = await orderPageCustomerBlock.getShippingAddress(page);
      const finalInvoiceAddress = await orderPageCustomerBlock.getInvoiceAddress(page);

      expect(
        finalDeliveryAddress.replace('Shipping', ''),
        'Invoice and delivery addresses shouldn\'t be the same',
      )
        .to.not.equal(finalInvoiceAddress.replace('Invoice', ''));
    });
  });
});
