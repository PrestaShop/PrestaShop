// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {createOrderSpecificProductTest} from '@commonTests/FO/classic/order';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import creditSlipsPage from '@pages/BO/orders/creditSlips';
import ordersPage from '@pages/BO/orders';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import OrderData from '@data/faker/order';
import ProductData from '@data/faker/product';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
  const product: ProductData = new ProductData({
    name: 'New product',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
  });
  // New order by customer
  const orderByCustomerData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product,
        quantity: 3,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create first product
  createProductTest(product, baseContext);

  // Pre-condition: Create order by default customer
  createOrderSpecificProductTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe(`Change the credit slip prefix to '${prefixToEdit}'`, async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.creditSlipsLink,
      );
      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it(`should change the credit slip prefix to ${prefixToEdit}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePrefix', baseContext);

      await creditSlipsPage.changePrefix(page, prefixToEdit);

      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check the new credit slip prefix', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await creditSlipsPage.goToSubMenu(
        page,
        creditSlipsPage.ordersParentLink,
        creditSlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it('should create a partial refund', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPartialRefund', baseContext);

      await orderPageTabListBlock.clickOnPartialRefund(page);

      const textMessage = await orderPageProductsBlock.addPartialRefundProduct(page, 1, 1);
      expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
    });

    it('should check the existence of the Credit slip document', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

      // Get document name
      const documentType = await orderPageTabListBlock.getDocumentType(page, 4);
      expect(documentType).to.be.equal('Credit slip');
    });

    it(`should check that the credit slip file name contain the prefix '${prefixToEdit}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedPrefixOnFileName', baseContext);

      // Get file name
      fileName = await orderPageTabListBlock.getFileName(page, 4);
      expect(fileName).to.contains(prefixToEdit);
    });
  });

  describe('Back to the default credit slip prefix value', async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPageToResetPrefix', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.creditSlipsLink,
      );

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should delete the credit slip prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrefix', baseContext);

      await creditSlipsPage.deletePrefix(page);

      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check that the new prefix does not exist in the credit slip file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageToCheckDeletedPrefix', baseContext);

      await creditSlipsPage.goToSubMenu(
        page,
        creditSlipsPage.ordersParentLink,
        creditSlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderToCheckDeletedPrefix', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check the credit slip file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedPrefix', baseContext);

      fileName = await orderPageTabListBlock.getFileName(page, 4);
      expect(fileName, 'Credit slip file name is not changed to default!').to.not.contains(prefixToEdit);
    });
  });

  // Post-condition
  deleteProductTest(product, baseContext);
});
