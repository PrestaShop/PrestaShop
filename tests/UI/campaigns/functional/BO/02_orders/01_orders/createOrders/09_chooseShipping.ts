// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderStatusData from '@data/faker/orderStatus';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_chooseShipping';

/*
Pre-condition:
- Enable and configure gift option
Scenario:
- Go to create order page and choose customer
- Check that shipping block is not visible before add product
- Choose carrier 'My carrier' and check details
- Enable free shipping and check details
- Add gift message, enable gift and enable recycling product
- Complete the order
- Check all details from view order page
Post-condition:
- Go back to default gift options configuration
- Delete cart rule free shipping
 */
describe('BO - Orders - Create order : Choose shipping', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to configure giftOptions
  const giftOptions = {
    wantedStatus: true,
    price: 10,
    tax: 'FR Taux réduit (10%)',
    isRecyclablePackage: true,
  };
  // Data to go back to default gift options
  const defaultGiftOptions = {
    wantedStatus: false,
    price: 0,
    tax: 'None',
    isRecyclablePackage: false,
  };
  const paymentMethodModuleName: string = PaymentMethods.checkPayment.moduleName;
  const orderStatus: OrderStatusData = OrderStatuses.paymentAccepted;
  const giftMessage: string = 'Gift message to test';

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition : configure gift options
  describe('PRE-TEST: Enable and configure gift options', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.orderSettingsLink,
      );
      await orderSettingsPage.closeSfToolBar(page);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it(`should configure gift options: price '€${giftOptions.price}' and tax '${giftOptions.tax}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'configureGiftOptions', baseContext);

      const result = await orderSettingsPage.setGiftOptions(
        page,
        giftOptions.wantedStatus,
        giftOptions.price,
        giftOptions.tax,
        giftOptions.isRecyclablePackage,
      );
      expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });
  });

  // 2 - Choose shipping
  describe('Choose shipping method', async () => {
    it('should check that shipping block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatShippingBlockNotVisible', baseContext);

      const isVisible = await addOrderPage.isShippingBlockVisible(page);
      expect(isVisible, 'Shipping block is visible!').to.eq(false);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const productToSelect = `${Products.demo_11.name} - €${Products.demo_11.price.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_11, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_11.thumbImage),
        expect(result.description).to.equal(Products.demo_11.name),
      ]);
    });

    it('should check that shipping block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatShippingBlockVisible', baseContext);

      const isVisible = await addOrderPage.isShippingBlockVisible(page);
      expect(isVisible, 'Shipping block is not visible!').to.eq(true);
    });

    it(`should choose the carrier '${Carriers.myCarrier.name}' and check shipping price`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingBlockContent', baseContext);

      const shippingPriceTTC = await addOrderPage.setDeliveryOption(
        page, `${Carriers.myCarrier.name} - Delivery next day!`,
      );
      expect(shippingPriceTTC).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock1', baseContext);

      const totalTaxExc = (Products.demo_12.priceTaxExcluded + Carriers.myCarrier.price).toFixed(2);
      const totalTaxInc = (Products.demo_12.price + Carriers.myCarrier.priceTTC).toFixed(2);

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalShipping).to.equal(`€${Carriers.myCarrier.price.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExc}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxInc}`),
      ]);
    });

    it('should enable free shipping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableFreeShipping', baseContext);

      await addOrderPage.setFreeShipping(page, true);

      const shippingPrice = await addOrderPage.getShippingCost(page);
      expect(shippingPrice).to.be.equal('€0.00');
    });

    it('should re-check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
      ]);
    });

    it('should enable \'Recycled packaging\' and \'Gift\' and add a gift message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

      await addOrderPage.setRecycledPackaging(page, true);
      await addOrderPage.setGiftMessage(page, giftMessage);
      await addOrderPage.setGift(page, true);
    });

    it('should enable gift and re-check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

      await addOrderPage.setGift(page, true);

      const tax = await basicHelper.percentage(giftOptions.price, 10);
      const totalTaxExc = (Products.demo_12.priceTaxExcluded + giftOptions.price).toFixed(2);
      const totalTaxInc = (Products.demo_12.price + giftOptions.price + tax).toFixed(2);

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExc}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxInc}`),
      ]);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await addOrderPage.setSummaryAndCreateOrder(page, paymentMethodModuleName, orderStatus);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);
    });

    it('should check \'Recycled packaging\' and \'gift wrapping\' badges', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBadges', baseContext);

      const recyclePackagingBadge = await orderPageTabListBlock.getSuccessBadge(page, 2);
      expect(recyclePackagingBadge).to.contain('Recycled packaging')
        .and.to.contain('Gift wrapping');
    });

    it('should check the gift message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftMessage', baseContext);

      const giftMessageText = await orderPageTabListBlock.getGiftMessage(page);
      expect(giftMessageText).to.be.equal(giftMessage);
    });
  });

  // Post-condition : Go back to default gift options configuration
  describe('POST-TEST: Go back to default configuration of gift options', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.orderSettingsLink,
      );
      await orderSettingsPage.closeSfToolBar(page);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should go back to default configuration', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackDefaultConfigureGiftOptions', baseContext);

      const result = await orderSettingsPage.setGiftOptions(
        page,
        defaultGiftOptions.wantedStatus,
        defaultGiftOptions.price,
        defaultGiftOptions.tax,
        defaultGiftOptions.isRecyclablePackage,
      );
      expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-Condition: delete cart rule free shipping
  deleteCartRuleTest('Free Shipping', baseContext);
});
