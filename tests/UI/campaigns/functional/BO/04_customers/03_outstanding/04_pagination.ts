// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {enableB2BTest, disableB2BTest} from '@commonTests/BO/shopParameters/enableDisableB2B';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import pages
import outstandingPage from '@pages/BO/customers/outstanding';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {Statuses} from '@data/demo/orderStatuses';
import {PaymentMethods} from '@data/demo/paymentMethods';
import type Order from '@data/types/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_outstanding_pagination';

/*
Pre-condition:
- Enable B2B
- Create 11 orders in FO
- Update orders status to payment accepted
Scenario:
- Change the items number to 10 per page
- Click on next button
- Click on previous
- Change the items number to 50 per page
Post-condition:
- Disable B2B
*/
describe('BO - Customers - Outstanding : Pagination of the outstanding page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used to get the number of outstanding
  let numberOutstanding: number;

  // Const used to get the least number of outstanding to display pagination
  const numberOfOrdersToCreate: number = 11;
  const orderByCustomerData: Order = {
    customer: DefaultCustomer,
    productId: 1,
    productQuantity: 1,
    paymentMethod: PaymentMethods.wirePayment.moduleName,
  };

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });
  describe('PRE-TEST: Create 11 orders on FO and change their status to payment accepted on BO', async () => {
    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const creationTests: number[] = new Array(numberOfOrdersToCreate).fill(0, 0, numberOfOrdersToCreate);
    creationTests.forEach((value: number, index: number) => {
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_${index}`);

      // Pre-condition: Update order status to payment accepted
      describe('Update order status to payment accepted', async () => {
        it('should go to Orders > Orders page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage_${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus_${index}`, baseContext);

          const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.paymentAccepted);
          await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatusBO_${index}`, baseContext);

          const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
          await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
        });
      });
    });
  });
  describe('Pagination next and previous', async () => {
    it('should go to BO > Customers > Outstanding page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );

      const pageTitle = await outstandingPage.getPageTitle(page);
      await expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

      await outstandingPage.resetFilter(page);

      numberOutstanding = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstanding).to.be.above(numberOfOrdersToCreate);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await outstandingPage.selectPaginationLimit(page, 10);
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await outstandingPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await outstandingPage.paginationPrevious(page);
      expect(paginationNumber, `Number of pages is not (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await outstandingPage.selectPaginationLimit(page, 50);
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });
  });

  // POST-Condition : Disable B2B
  disableB2BTest(baseContext);
});
