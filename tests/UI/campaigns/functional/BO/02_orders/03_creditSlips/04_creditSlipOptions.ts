// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {createOrderSpecificProductTest} from '@commonTests/FO/classic/order';

import {
  boCreditSlipsPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerOrder,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_orders_creditSlips_creditSlipOptions';

/*
Pre-condition
- Create product
- Create order from FO
Scenario
- Edit credit slip prefix
- Change the Order status to shipped
- Add a partial refund
- Check the credit slip file name
- Delete the slip prefix value
- Check the credit slip file name
Post-condition
- Delete product
 */
describe('BO - Orders - Credit slips: Credit slip options', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const prefixToEdit: string = 'CreSlip';
  const product: FakerProduct = new FakerProduct({
    name: 'New product',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
  });
  // New order by customer
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product,
        quantity: 3,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create first product
  createProductTest(product, baseContext);

  // Pre-condition: Create order by default customer
  createOrderSpecificProductTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe(`Change the credit slip prefix to '${prefixToEdit}'`, async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.creditSlipsLink,
      );
      await boCreditSlipsPage.closeSfToolBar(page);

      const pageTitle = await boCreditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);
    });

    it(`should change the credit slip prefix to ${prefixToEdit}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePrefix', baseContext);

      await boCreditSlipsPage.changePrefix(page, prefixToEdit);

      const textMessage = await boCreditSlipsPage.saveCreditSlipOptions(page);
      expect(textMessage).to.contains(boCreditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check the new credit slip prefix', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boCreditSlipsPage.goToSubMenu(
        page,
        boCreditSlipsPage.ordersParentLink,
        boCreditSlipsPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should create a partial refund', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPartialRefund', baseContext);

      await boOrdersViewBlockTabListPage.clickOnPartialRefund(page);

      const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(page, 1, 1);
      expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
    });

    it('should check the existence of the Credit slip document', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

      // Get document name
      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 4);
      expect(documentType).to.be.equal('Credit slip');
    });

    it(`should check that the credit slip file name contain the prefix '${prefixToEdit}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedPrefixOnFileName', baseContext);

      // Get file name
      fileName = await boOrdersViewBlockTabListPage.getFileName(page, 4);
      expect(fileName).to.contains(prefixToEdit);
    });
  });

  describe('Back to the default credit slip prefix value', async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPageToResetPrefix', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.creditSlipsLink,
      );

      const pageTitle = await boCreditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);
    });

    it('should delete the credit slip prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrefix', baseContext);

      await boCreditSlipsPage.deletePrefix(page);

      const textMessage = await boCreditSlipsPage.saveCreditSlipOptions(page);
      expect(textMessage).to.contains(boCreditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check that the new prefix does not exist in the credit slip file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageToCheckDeletedPrefix', baseContext);

      await boCreditSlipsPage.goToSubMenu(
        page,
        boCreditSlipsPage.ordersParentLink,
        boCreditSlipsPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderToCheckDeletedPrefix', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should check the credit slip file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedPrefix', baseContext);

      fileName = await boOrdersViewBlockTabListPage.getFileName(page, 4);
      expect(fileName, 'Credit slip file name is not changed to default!').to.not.contains(prefixToEdit);
    });
  });

  // Post-condition
  deleteProductTest(product, baseContext);
});
