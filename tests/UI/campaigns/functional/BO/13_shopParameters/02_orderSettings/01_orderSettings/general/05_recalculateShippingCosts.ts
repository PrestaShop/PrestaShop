// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

import {
  boDashboardPage,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  FakerOrderShipping,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_recalculateShippingCosts';

describe('BO - Shop Parameters - Order Settings : Recalculate shipping costs after editing the order', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  const shippingDetailsData: FakerOrderShipping = new FakerOrderShipping({
    trackingNumber: '0523698',
    carrier: dataCarriers.myCarrier.name,
    carrierID: dataCarriers.myCarrier.id,
  });

  const editShippingDetailsData: FakerOrderShipping = new FakerOrderShipping({
    trackingNumber: '0523698',
    carrier: dataCarriers.clickAndCollect.name,
    carrierID: dataCarriers.clickAndCollect.id,
  });

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

  describe('Set recalculate shipping costs after editing the order', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const tests = [
      {
        args: {
          action: 'enable',
          toEnable: true,
          carrierData: shippingDetailsData,
          cost: '€8.40',
        },
      },
      {
        args: {
          action: 'disable',
          toEnable: false,
          carrierData: editShippingDetailsData,
          cost: '€8.40',
        },
      },
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop Parameters > Order Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderSettingsPage_${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.orderSettingsLink,
        );
        await orderSettingsPage.closeSfToolBar(page);

        const pageTitle = await orderSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} final summary`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}FinalSummary`, baseContext);

        const result = await orderSettingsPage.recalculateShippingCostAfterEditingOrder(page, test.args.toEnable);
        expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage_${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should view the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewOrderPage_${index}`, baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it('should click on \'Carriers\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `displayCarriersTab_${index}`, baseContext);

        const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
        expect(isTabOpened).to.eq(true);
      });

      it('should click on \'Edit\' link and check the modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnEditLink_${index}`, baseContext);

        const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
        expect(isModalVisible, 'Edit shipping modal is not visible!').to.eq(true);
      });

      it('should update the carrier and add a tracking number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateTrackingNumber_${index}`, baseContext);

        const textResult = await orderPageTabListBlock.setShippingDetails(page, test.args.carrierData!);
        expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
      });

      it('should check the updated carrier details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUpdatedCarrierDetails_${index}`, baseContext);

        await orderPageTabListBlock.goToCarriersTab(page);

        const result = await orderPageTabListBlock.getCarrierDetails(page);
        await Promise.all([
          expect(result.carrier).to.equal(test.args.carrierData.carrier),
          expect(result.shippingCost).to.equal(test.args.cost),
        ]);
      });
    });
  });
});
