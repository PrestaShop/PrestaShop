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
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_outstanding_viewInvoice';

/*
Pre-condition:
- Enable B2B
- Create order in FO
- Update order status to payment accepted
Scenario:
- View invoice from the outstanding page
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : View invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used for the last order reference created
  let orderReference: string;
  // Variable used for the temporary invoice file
  let filePath: string|null;
  // New order by customer data
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

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Update order status to payment accepted
  describe('PRE-TEST: Update order status to payment accepted', async () => {
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

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset filter and get the last reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrder', baseContext);

      await boOrdersPage.resetFilter(page);

      orderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
      expect(orderReference).to.not.equal(null);
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.paymentAccepted);
      expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
    });

    it('should check that the status is updated successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusBO', baseContext);

      const orderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
      expect(orderStatus, 'Order status was not updated').to.equal(dataOrderStatuses.paymentAccepted.name);
    });
  });

  // 1 - View invoice from the outstanding
  describe('View invoice from the outstanding page', async () => {
    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.outstandingLink,
      );
      await boOutstandingPage.closeSfToolBar(page);

      const pageTitle = await boOutstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOutstandingPage.pageTitle);
    });

    it('should reset filter and get the last outstanding ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await boOutstandingPage.resetFilter(page);

      const outstandingId = parseInt(
        await boOutstandingPage.getTextColumn(page, 'id_invoice', 1),
        10,
      );
      expect(outstandingId).to.be.at.least(1);
    });

    it('should view the Invoice and check the order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await boOutstandingPage.viewInvoice(page, 'invoice', 1);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'The file is not existing!').to.eq(true);
    });

    it('should check reference in the invoice pdf file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceReference', baseContext);

      // Check Reference in pdf
      const referenceOrder = await utilsFile.isTextInPDF(filePath, orderReference);
      expect(referenceOrder).to.eq(true);
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
