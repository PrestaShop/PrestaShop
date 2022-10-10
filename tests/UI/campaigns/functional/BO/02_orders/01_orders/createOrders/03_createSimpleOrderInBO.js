require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const orderPageCustomerBlock = require('@pages/BO/orders/view/customerBlock');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');

// Import data
// Customer
const {DefaultCustomer} = require('@data/demo/customer');

// Payment Methods
const {PaymentMethods} = require('@data/demo/paymentMethods');

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
  customer: DefaultCustomer,
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
  paymentMethod: PaymentMethods.checkPayment.moduleName,
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
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await ordersPage.goToCreateOrderPage(page);
    const pageTitle = await addOrderPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addOrderPage.pageTitle);
  });

  describe('Create order and check result', async () => {
    it('should create the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      await addOrderPage.createOrder(page, orderToMake);
      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderStatus', baseContext);

      const orderStatus = await orderPageProductsBlock.getOrderStatus(page);
      await expect(orderStatus).to.equal(orderToMake.orderStatus.status);
    });

    it('should check order total price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPrice', baseContext);

      const totalPrice = await orderPageProductsBlock.getOrderTotalPrice(page);
      await expect(totalPrice).to.equal(orderToMake.totalPrice);
    });

    it('should check order shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await orderPageCustomerBlock.getShippingAddress(page);
      await expect(shippingAddress)
        .to.contain(orderToMake.addressValue.firstName)
        .and.to.contain(orderToMake.addressValue.lastName)
        .and.to.contain(orderToMake.addressValue.address)
        .and.to.contain(orderToMake.addressValue.postalCode)
        .and.to.contain(orderToMake.addressValue.city)
        .and.to.contain(orderToMake.addressValue.country);
    });

    it('should check order invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const invoiceAddress = await orderPageCustomerBlock.getInvoiceAddress(page);
      await expect(invoiceAddress)
        .to.contain(orderToMake.addressValue.firstName)
        .and.to.contain(orderToMake.addressValue.lastName)
        .and.to.contain(orderToMake.addressValue.address)
        .and.to.contain(orderToMake.addressValue.postalCode)
        .and.to.contain(orderToMake.addressValue.city)
        .and.to.contain(orderToMake.addressValue.country);
    });

    it('should check products names in cart list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNames', baseContext);

      for (let i = 1; i <= orderToMake.products.length; i++) {
        const productName = await orderPageProductsBlock.getProductNameFromTable(page, i);
        await expect(productName).to.contain(orderToMake.products[i - 1].value.name);
      }
    });
  });

  // Post-Condition: delete cart rules
  deleteCartRuleTest('Free Shipping', baseContext);
});
