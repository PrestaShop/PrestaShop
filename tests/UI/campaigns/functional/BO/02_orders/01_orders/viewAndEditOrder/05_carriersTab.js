require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const {getDateFormat} = require('@utils/date');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');
const {Carriers} = require('@data/demo/carriers');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_carriersTab';

let browserContext;
let page;
const today = getDateFormat('mm/dd/yyyy');

const shippingDetailsData = {
  trackingNumber: '0523698',
  carrier: Carriers.myCarrier.name,
  carrierID: Carriers.myCarrier.id,
  shippingCost: '€8.40',
};

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition :
- Create order by default customer
Scenario :
- Check carriers number from Carriers tab
- Check shipping details
- Update carrier and tracking number and check details
 */

describe('BO - Orders - View and edit order : Check order carriers tab', async () => {
  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });
  });

  // 2 - Check carriers tab
  describe('Check carriers tab', async () => {
    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check that the carriers number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

      const carriersNumber = await orderPageTabListBlock.getCarriersNumber(page);
      await expect(carriersNumber).to.be.equal(1);
    });

    it('should check the carrier details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierDetails', baseContext);

      const result = await orderPageTabListBlock.getCarrierDetails(page);
      await Promise.all([
        expect(result.date).to.equal(today),
        expect(result.carrier).to.equal(Carriers.default.name),
        expect(result.weight).to.equal(`${Products.demo_1.weight}00 kg`),
        expect(result.shippingCost).to.equal('€0.00'),
        expect(result.trackingNumber).to.equal(''),
      ]);
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
      await expect(isModalVisible, 'Edit shipping modal is not visible!').to.be.true;
    });

    it('should update the carrier and add a tracking number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTrackingNumber', baseContext);

      const textResult = await orderPageTabListBlock.setShippingDetails(page, shippingDetailsData);
      await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it('should check the updated carrier details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCarrierDetails', baseContext);

      await orderPageTabListBlock.goToCarriersTab(page);

      const result = await orderPageTabListBlock.getCarrierDetails(page);
      await Promise.all([
        expect(result.date).to.equal(today),
        expect(result.carrier).to.equal(shippingDetailsData.carrier),
        expect(result.weight).to.equal(`${Products.demo_1.weight}00 kg`),
        expect(result.shippingCost).to.equal(shippingDetailsData.shippingCost),
        expect(result.trackingNumber).to.equal(shippingDetailsData.trackingNumber),
      ]);
    });
  });
});
