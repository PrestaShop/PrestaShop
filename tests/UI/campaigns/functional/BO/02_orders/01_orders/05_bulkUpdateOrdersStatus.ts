// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/createOrder';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders/index';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {PaymentMethods} from '@data/demo/paymentMethods';
import {Statuses} from '@data/demo/orderStatuses';
import type Order from '@data/types/order';

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

  const orderByCustomerData: Order = {
    customer: DefaultCustomer,
    productId: 1,
    productQuantity: 1,
    paymentMethod: PaymentMethods.wirePayment.moduleName,
  };

  // Pre-condition: Create 2 orders in FO
  const orderNumber = 2;

  for (let i = 1; i <= orderNumber; i++) {
    createOrderByCustomerTest(orderByCustomerData, `${baseContext}_${i}`);
  }

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Update orders status in BO', async () => {
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should update orders status with bulk action', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkUpdateOrdersStatus', baseContext);

      const textResult = await ordersPage.bulkUpdateOrdersStatus(
        page,
        Statuses.paymentAccepted.status,
        false,
        [1, 2],
      );
      await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });

    ['first', 'second'].forEach((arg, index) => {
      it(`should check the ${arg} order status`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderStatus${index + 1}`, baseContext);

        const orderStatus = await ordersPage.getTextColumn(page, 'osname', index + 1);
        await expect(orderStatus, 'Order status is not correct').to.equal(Statuses.paymentAccepted.status);
      });
    });
  });
});
