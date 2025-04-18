import testContext from '@utils/testContext';
import {expect} from 'chai';

import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOutstandingPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });
  describe('PRE-TEST: Create 11 orders on FO and change their status to payment accepted on BO', async () => {
    it('should login to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    const creationTests: number[] = new Array(numberOfOrdersToCreate).fill(0, 0, numberOfOrdersToCreate);
    creationTests.forEach((value: number, index: number) => {
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_${index}`);

      // Pre-condition: Update order status to payment accepted
      describe('Update order status to payment accepted', async () => {
        it('should go to Orders > Orders page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage_${index}`, baseContext);

          if (index === 0) {
            await boDashboardPage.goToSubMenu(
              page,
              boDashboardPage.ordersParentLink,
              boDashboardPage.ordersLink,
            );
          } else {
            await boOrdersPage.reloadPage(page);
          }

          const pageTitle = await boOrdersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrdersPage.pageTitle);
        });

        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus_${index}`, baseContext);

          const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.paymentAccepted);
          expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatusBO_${index}`, baseContext);

          const orderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
          expect(orderStatus, 'Order status was not updated').to.equal(dataOrderStatuses.paymentAccepted.name);
        });
      });
    });
  });
  describe('Pagination next and previous', async () => {
    it('should go to BO > Customers > Outstanding page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.outstandingLink,
      );

      const pageTitle = await boOutstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOutstandingPage.pageTitle);
    });

    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

      await boOutstandingPage.resetFilter(page);

      numberOutstanding = await boOutstandingPage.getNumberOutstanding(page);
      expect(numberOutstanding).to.be.above(numberOfOrdersToCreate);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await boOutstandingPage.selectPaginationLimit(page, 10);
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boOutstandingPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boOutstandingPage.paginationPrevious(page);
      expect(paginationNumber, `Number of pages is not (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boOutstandingPage.selectPaginationLimit(page, 50);
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });
  });

  // POST-Condition : Disable B2B
  disableB2BTest(baseContext);
});
