require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const DeliverySlipOptionsFaker = require('@data/faker/deliverySlipOptions');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_deliverSlipsOptions_deliverySlipPrefix';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let fileName;

const deliverySlipData = new DeliverySlipOptionsFaker();
const defaultPrefix = '#DE';

/*
Edit delivery slip prefix
Change the Order status to Shipped
Check the delivery slip file name
Back to the default delivery slip prefix value
 */
describe('BO - Orders - Delivery slips : Update delivery slip prefix and check the generated file name', async () => {
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

  describe('Update the delivery slip prefix', async () => {
    it('should go to \'Orders > Delivery slip\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.deliverySlipslink,
      );

      await deliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it(`should update the delivery slip prefix to ${deliverySlipData.prefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDeliverySlipsPrefix', baseContext);

      await deliverySlipsPage.changePrefix(page, deliverySlipData.prefix);
      const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
      await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe(`Update the order status to '${Statuses.shipped.status}' and check the file name`, async () => {
    it('should go to \'Orders > Orders\' page', async function () {
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
      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it(`should check that the delivery slip file name contain '${deliverySlipData.prefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentNamePrefix', baseContext);

      // Get delivery slips filename
      fileName = await viewOrderPage.getFileName(page, 3);
      expect(fileName).to.contains(deliverySlipData.prefix.replace('#', '').trim());
    });
  });

  describe(`Back to the default delivery slip prefix value '${defaultPrefix}'`, async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPageBackToDefaultValue', baseContext);

      await viewOrderPage.goToSubMenu(
        page,
        viewOrderPage.ordersParentLink,
        viewOrderPage.deliverySlipslink,
      );

      await deliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it(`should update the delivery slip prefix to '${defaultPrefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultPrefixValue', baseContext);

      await deliverySlipsPage.changePrefix(page, defaultPrefix);
      const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
      await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
    });
  });
});
