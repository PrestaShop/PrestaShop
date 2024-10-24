// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

// Import BO pages
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext = 'functional_BO_orders_orders_bulkOpenInNewTab';

/*
Pre-condition:
- Create 2 orders in FO
Scenario:
- Go to BO > Orders page
- Bulk open in new tabs the 2 last orders
- Check the 2 orders (Check customer block)
Post-condition:
- Delete the 2 created guest customers
 */
describe('BO - Orders : Bulk open on new tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstCustomerData: FakerCustomer = new FakerCustomer();
  const secondCustomerData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  const firstOrderByGuestData: FakerOrder = new FakerOrder({
    customer: firstCustomerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const secondOrderByGuestData: FakerOrder = new FakerOrder({
    customer: secondCustomerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create first order in FO
  createOrderByGuestTest(firstOrderByGuestData, `${baseContext}_preTest_1`);

  // Pre-condition: Create second order in FO
  createOrderByGuestTest(secondOrderByGuestData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Open on new tab by bulk actions', async () => {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should click on \'Open in new tabs\' with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkOpenInNewTabs', baseContext);

      page = await boOrdersPage.bulkOpenInNewTabs(page, false, [1, 2]);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the first opened order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      expect(customerInfo).to.contains(secondCustomerData.socialTitle);
      expect(customerInfo).to.contains(secondCustomerData.firstName);
      expect(customerInfo).to.contains(secondCustomerData.lastName);
    });

    it('should close the tab and check that the second order page is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFirstOrderPage', baseContext);

      page = await orderPageCustomerBlock.closePage(browserContext, page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the second order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      expect(customerInfo).to.contains(firstCustomerData.socialTitle);
      expect(customerInfo).to.contains(firstCustomerData.firstName);
      expect(customerInfo).to.contains(firstCustomerData.lastName);
    });
  });

  // Post-condition: Delete first guest customers
  deleteCustomerTest(firstCustomerData, `${baseContext}_postTest_1`);

  // Post-condition: Delete second guest customers
  deleteCustomerTest(secondCustomerData, `${baseContext}_postTest_2`);
});
