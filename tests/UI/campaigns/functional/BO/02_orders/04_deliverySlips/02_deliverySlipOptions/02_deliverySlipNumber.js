require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
const DeliverySlipOptionsFaker = require('@data/faker/deliverySlipOptions');

// Importing test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_deliverSlipsOptions_deliverySlipNumber';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let fileName;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 5,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};
const deliverySlipData = new DeliverySlipOptionsFaker();

/*
Pre-condition:
- Create order in FO
Scenario:
- Edit Delivery slip number
- Change the Order status to Shipped
- Check the delivery slip file name
 */

describe('BO - Orders - Delivery slips : Update \'Delivery slip number\'', async () => {
  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

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

  describe('Update the Delivery slip number', async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPageToUpdateNumber', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.deliverySlipslink,
      );

      await deliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it('should change the Delivery slip number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDeliverySlipsNumber', baseContext);

      await deliverySlipsPage.changeNumber(page, deliverySlipData.number);
      const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
      await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe('Create a delivery slip and check the update data', async () => {
    it('should go to the \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await deliverySlipsPage.goToSubMenu(
        page,
        deliverySlipsPage.ordersParentLink,
        deliverySlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);
      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check that the delivery slip file name contain the \'Delivery slip number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipsDocumentName', baseContext);

      // Get delivery slips filename
      fileName = await orderPageTabListBlock.getFileName(page, 3);
      expect(fileName).to.contains(deliverySlipData.number);
    });
  });
});
