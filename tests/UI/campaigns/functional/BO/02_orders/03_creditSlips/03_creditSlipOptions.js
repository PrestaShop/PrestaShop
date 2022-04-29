require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createProductTest, deleteProductTest} = require('@commonTests/BO/catalog/createDeleteProduct');
const {createOrderSpecificProductTest} = require('@commonTests/FO/createOrder');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');

// Import demo data
const {Statuses} = require('@data/demo/orderStatuses');
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import faker data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_creditSlipOptions';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let fileName;
const prefixToEdit = 'CreSlip';

// Product to create
const product = new ProductFaker({
  name: 'New product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
});

// New order by customer
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: product.name,
  productQuantity: 3,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

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
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it(`should change the credit slip prefix to ${prefixToEdit}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePrefix', baseContext);

      await creditSlipsPage.changePrefix(page, prefixToEdit);
      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      await expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should create a partial refund', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPartialRefund', baseContext);

      await orderPageTabListBlock.clickOnPartialRefund(page);

      const textMessage = await orderPageProductsBlock.addPartialRefundProduct(page, 1, 1);
      await expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
    });

    it('should check the existence of the Credit slip document', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

      // Get document name
      const documentType = await orderPageTabListBlock.getDocumentType(page, 4);
      await expect(documentType).to.be.equal('Credit slip');
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
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should delete the credit slip prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrefix', baseContext);

      await creditSlipsPage.deletePrefix(page);

      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      await expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderToCheckDeletedPrefix', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
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
