// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByGuestTest} from '@commonTests/FO/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext = 'functional_BO_orders_orders_bulkOpenInNewTab';

/*
Pre-condition:
- Create 2 orders in FO
Scenario:
- Go to BO > Orders page
- Bulk open in new tabs the 2 last orders
- Check the 2 orders (Check customer block)
Post-condition:
- Delete the 2 created guest customers
 */
describe('BO - Orders : Bulk open on new tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstCustomerData: CustomerData = new CustomerData();
  const secondCustomerData: CustomerData = new CustomerData();
  const addressData: AddressData = new AddressData({country: 'France'});
  const firstOrderByGuestData: OrderData = new OrderData({
    customer: firstCustomerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });
  const secondOrderByGuestData: OrderData = new OrderData({
    customer: secondCustomerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create first order in FO
  createOrderByGuestTest(firstOrderByGuestData, `${baseContext}_preTest_1`);

  // Pre-condition: Create second order in FO
  createOrderByGuestTest(secondOrderByGuestData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Open on new tab by bulk actions', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should click on \'Open in new tabs\' with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkOpenInNewTabs', baseContext);

      page = await ordersPage.bulkOpenInNewTabs(page, false, [1, 2]);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the first opened order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      expect(customerInfo).to.contains(secondCustomerData.socialTitle);
      expect(customerInfo).to.contains(secondCustomerData.firstName);
      expect(customerInfo).to.contains(secondCustomerData.lastName);
    });

    it('should close the tab and check that the second order page is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFirstOrderPage', baseContext);

      page = await orderPageCustomerBlock.closePage(browserContext, page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the second order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      expect(customerInfo).to.contains(firstCustomerData.socialTitle);
      expect(customerInfo).to.contains(firstCustomerData.firstName);
      expect(customerInfo).to.contains(firstCustomerData.lastName);
    });
  });

  // Post-condition: Delete first guest customers
  deleteCustomerTest(firstCustomerData, `${baseContext}_postTest_1`);

  // Post-condition: Delete second guest customers
  deleteCustomerTest(secondCustomerData, `${baseContext}_postTest_2`);
});
