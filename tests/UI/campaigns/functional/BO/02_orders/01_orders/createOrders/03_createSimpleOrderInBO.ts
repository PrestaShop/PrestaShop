// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';

// Import data
import Addresses from '@data/demo/address';
import Carriers from '@data/demo/carriers';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_createSimpleOrderInBO';

/*
Scenario:
- Choose the default customer from Create order page
- Add products to cart
- Choose addresses for delivery and invoice
- Choose payment status
- Set order status and save the order
- From view order page check these details :
  - Order status
  - Total price
  - Shipping address
  - Invoice address
  - Products names
Post-condition:
- Delete Free shipping cart rule
 */
describe('BO - Orders - Create order : Create simple order in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderToMake: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_5,
        quantity: 4,
      },
    ],
    deliveryAddress: Addresses.second,
    invoiceAddress: Addresses.second,
    deliveryOption: {
      name: `${Carriers.default.name} - ${Carriers.default.delay}`,
      freeShipping: true,
    },
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.paymentAccepted,
    totalPrice: (Products.demo_5.priceTaxExcluded * 4) * 1.2, // Price tax included
  });

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

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );
    await ordersPage.closeSfToolBar(page);

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await ordersPage.goToCreateOrderPage(page);

    const pageTitle = await addOrderPage.getPageTitle(page);
    expect(pageTitle).to.contains(addOrderPage.pageTitle);
  });

  describe('Create order and check result', async () => {
    it('should create the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      await addOrderPage.createOrder(page, orderToMake);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderStatus', baseContext);

      const orderStatus = await orderPageProductsBlock.getOrderStatus(page);
      expect(orderStatus).to.equal(orderToMake.status.name);
    });

    it('should check order total price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPrice', baseContext);

      const totalPrice = await orderPageProductsBlock.getOrderTotalPrice(page);
      expect(totalPrice).to.equal(orderToMake.totalPrice);
    });

    it('should check order shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await orderPageCustomerBlock.getShippingAddress(page);
      expect(shippingAddress)
        .to.contain(orderToMake.deliveryAddress.firstName)
        .and.to.contain(orderToMake.deliveryAddress.lastName)
        .and.to.contain(orderToMake.deliveryAddress.address)
        .and.to.contain(orderToMake.deliveryAddress.postalCode)
        .and.to.contain(orderToMake.deliveryAddress.city)
        .and.to.contain(orderToMake.deliveryAddress.country);
    });

    it('should check order invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const invoiceAddress = await orderPageCustomerBlock.getInvoiceAddress(page);
      expect(invoiceAddress)
        .to.contain(orderToMake.deliveryAddress.firstName)
        .and.to.contain(orderToMake.deliveryAddress.lastName)
        .and.to.contain(orderToMake.deliveryAddress.address)
        .and.to.contain(orderToMake.deliveryAddress.postalCode)
        .and.to.contain(orderToMake.deliveryAddress.city)
        .and.to.contain(orderToMake.deliveryAddress.country);
    });

    it('should check products names in cart list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNames', baseContext);

      for (let i = 1; i <= orderToMake.products.length; i++) {
        const productName = await orderPageProductsBlock.getProductNameFromTable(page, i);
        expect(productName).to.contain(orderToMake.products[i - 1].product.name);
      }
    });
  });

  // Post-Condition: delete cart rules
  deleteCartRuleTest('Free Shipping', baseContext);
});
