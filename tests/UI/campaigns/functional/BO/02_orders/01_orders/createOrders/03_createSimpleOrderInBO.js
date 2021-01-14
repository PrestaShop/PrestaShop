require('module-alias/register');

const helper = require('@utils/helpers');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const viewOrderPage = require('@pages/BO/orders/view');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data

// Customer
const {DefaultAccount} = require('@data/demo/customer');

// Products
const {Products} = require('@data/demo/products');

// Order status
const {Statuses} = require('@data/demo/orderStatuses');

// Carriers
const {Carriers} = require('@data/demo/carriers');

// Addresses
const addresses = require('@data/demo/address');

// Order to make data
const orderToMake = {
  customer: DefaultAccount,
  products: [
    {value: Products.demo_5, quantity: 4},
  ],
  deliveryAddress: 'Mon adresse',
  invoiceAddress: 'Mon adresse',
  addressValue: addresses.second,
  deliveryOption: {
    name: `${Carriers.default.name} - ${Carriers.default.delay}`,
    freeShipping: true,
  },
  paymentMethod: 'Payments by check',
  orderStatus: Statuses.paymentAccepted,

  totalPrice: (Products.demo_5.price * 4) * 1.2, // Price tax included
};

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_createSimpleOrderInBO';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/*
Go to create order page
Search and choose a customer
Add products to cart
Choose address for delivery and invoice
Choose payment status
Set order status and save the order
Check order status from view order page
Check order total price from view order page
 */
describe('Create simple order in BO', async () => {
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

  it('should go to orders page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );

    await ordersPage.closeSfToolBar(page);
    const pageTitle = await ordersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await ordersPage.goToCreateOrderPage(page);
    const pageTitle = await addOrderPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addOrderPage.pageTitle);
  });

  describe('Choose create order and check result', async () => {
    it('should create te order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      await addOrderPage.createOrder(page, orderToMake);
      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contain(viewOrderPage.pageTitle);
    });

    it('should check order status after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderStatus', baseContext);

      const orderStatus = await viewOrderPage.getOrderStatus(page);
      await expect(orderStatus).to.equal(orderToMake.orderStatus.status);
    });

    it('should check order total price after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPrice', baseContext);

      const totalPrice = await viewOrderPage.getOrderTotalPrice(page);
      await expect(totalPrice).to.equal(orderToMake.totalPrice);
    });

    it('should check order shipping address after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await viewOrderPage.getShippingAddress(page);
      await expect(shippingAddress)
        .to.contain(orderToMake.addressValue.firstName)
        .and.to.contain(orderToMake.addressValue.lastName)
        .and.to.contain(orderToMake.addressValue.address)
        .and.to.contain(orderToMake.addressValue.zipCode)
        .and.to.contain(orderToMake.addressValue.city)
        .and.to.contain(orderToMake.addressValue.country);
    });

    it('should check order invoice address after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const invoiceAddress = await viewOrderPage.getInvoiceAddress(page);
      await expect(invoiceAddress)
        .to.contain(orderToMake.addressValue.firstName)
        .and.to.contain(orderToMake.addressValue.lastName)
        .and.to.contain(orderToMake.addressValue.address)
        .and.to.contain(orderToMake.addressValue.zipCode)
        .and.to.contain(orderToMake.addressValue.city)
        .and.to.contain(orderToMake.addressValue.country);
    });

    it('should check products names in cart list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNames', baseContext);

      for (let i = 1; i <= orderToMake.products.length; i++) {
        const productName = await viewOrderPage.getProductNameFromTable(page, i);
        await expect(productName).to.contain(orderToMake.products[i - 1].value.name);
      }
    });
  });
});
