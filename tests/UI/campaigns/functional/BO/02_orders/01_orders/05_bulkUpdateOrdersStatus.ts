// Import utils
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import ordersPage from '@pages/BO/orders';

import {
  boDashboardPage,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_bulkUpdateOrdersStatus';

/*
Pre-condition:
- Create 2 orders in FO
Scenario:
- Go to BO and update orders created status by bulk actions
- Check orders new status
 */
describe('BO - Orders : Bulk update orders status', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create 2 orders in FO
  const orderNumber = 2;

  for (let i = 1; i <= orderNumber; i++) {
    createOrderByCustomerTest(orderByCustomerData, `${baseContext}_${i}`);
  }

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Update orders status in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should update orders status with bulk action', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkUpdateOrdersStatus', baseContext);

      const textResult = await ordersPage.bulkUpdateOrdersStatus(
        page,
        dataOrderStatuses.paymentAccepted.name,
        false,
        [1, 2],
      );
      expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });

    ['first', 'second'].forEach((arg: string, index: number) => {
      it(`should check the ${arg} order status`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderStatus${index + 1}`, baseContext);

        const orderStatus = await ordersPage.getTextColumn(page, 'osname', index + 1);
        expect(orderStatus, 'Order status is not correct').to.equal(dataOrderStatuses.paymentAccepted.name);
      });
    });
  });
});
