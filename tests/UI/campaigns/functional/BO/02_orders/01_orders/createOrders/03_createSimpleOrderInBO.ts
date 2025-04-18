import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boOrdersViewBlockCustomersPage,
  boOrdersViewBlockProductsPage,
  type BrowserContext,
  dataAddresses,
  dataCarriers,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const orderToMake: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_5,
        quantity: 4,
      },
    ],
    deliveryAddress: dataAddresses.address_2,
    invoiceAddress: dataAddresses.address_2,
    deliveryOption: {
      name: `${dataCarriers.clickAndCollect.name} - ${dataCarriers.clickAndCollect.transitName}`,
      freeShipping: true,
    },
    paymentMethod: dataPaymentMethods.checkPayment,
    status: dataOrderStatuses.paymentAccepted,
    totalPrice: (dataProducts.demo_5.priceTaxExcluded * 4) * 1.2, // Price tax included
  });

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

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );
    await boOrdersPage.closeSfToolBar(page);

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await boOrdersPage.goToCreateOrderPage(page);

    const pageTitle = await boOrdersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
  });

  describe('Create order and check result', async () => {
    it('should create the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      await boOrdersCreatePage.createOrder(page, orderToMake);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contain(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderStatus', baseContext);

      const orderStatus = await boOrdersViewBlockProductsPage.getOrderStatus(page);
      expect(orderStatus).to.equal(orderToMake.status.name);
    });

    it('should check order total price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPrice', baseContext);

      const totalPrice = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalPrice).to.equal(orderToMake.totalPrice);
    });

    it('should check order shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await boOrdersViewBlockCustomersPage.getShippingAddress(page);
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

      const invoiceAddress = await boOrdersViewBlockCustomersPage.getInvoiceAddress(page);
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
        const productName = await boOrdersViewBlockProductsPage.getProductNameFromTable(page, i);
        expect(productName).to.contain(orderToMake.products[i - 1].product.name);
      }
    });
  });

  // Post-Condition: delete cart rules
  deleteCartRuleTest('Free Shipping', baseContext);
});
