// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

// Import BO pages
import viewCustomerPage from '@pages/BO/customers/view';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_viewGuest';

/*
Pre-condition:
- Create order by guest
Scenario:
- Go to orders page
- Filter by guest email
- Click on guest link on grid
- Check that View customer(guest) page is displayed
Post-condition
- Delete guest account
 */
describe('BO - Orders : View guest from orders page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  // New order by guest data
  const orderByGuestData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by guest in FO
  createOrderByGuestTest(orderByGuestData, baseContext);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('View guest from orders page', async () => {
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
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should filter order by customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(
        page,
        'input',
        'customer',
        customerData.lastName,
      );

      const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);
      expect(numberOfOrders).to.be.at.least(1);
    });

    it('should check guest link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCustomer', baseContext);

      // Click on customer link first row
      page = await ordersPage.viewCustomer(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to
        .contains(viewCustomerPage.pageTitle(`${customerData.firstName[0]}. ${customerData.lastName}`));
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
