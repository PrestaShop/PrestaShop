import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

import {
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const orderByGuestData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by guest in FO
  createOrderByGuestTest(orderByGuestData, baseContext);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('View guest from orders page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should filter order by customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await boOrdersPage.filterOrders(
        page,
        'input',
        'customer',
        customerData.lastName,
      );

      const numberOfOrders = await boOrdersPage.getNumberOfElementInGrid(page);
      expect(numberOfOrders).to.be.at.least(1);
    });

    it('should check guest link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCustomer', baseContext);

      // Click on customer link first row
      page = await boOrdersPage.viewCustomer(page, 1);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to
        .contains(boCustomersViewPage.pageTitle(`${customerData.firstName[0]}. ${customerData.lastName}`));
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
