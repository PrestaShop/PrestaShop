require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const customersPage = require('@pages/BO/customers');

// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const addressData = new AddressFaker();

const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_customerBlock';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCustomers = 0;

const firstCustomerData = new CustomerFaker({password: ''});
const secondCustomerData = new CustomerFaker({password: ''});

/*

*/
describe('Check customer block in view order page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foHomePage.goToFo(page);
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  // 1 - create 2 orders
  [
    {args: {customer: firstCustomerData}},
    {args: {customer: secondCustomerData}},
  ].forEach((test, index) => {
    describe(`Create order by '${test.args.customer.firstName} ${test.args.customer.lastName}' in FO`, async () => {
      it('should add product to cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foHomePage.goToHomePage(page);

        // Go to the fourth product page
        await foHomePage.goToProductPage(page, 4);

        // Add the created product to the cart
        await foProductPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await foCartPage.clickOnProceedToCheckout(page);

        // Go to checkout page
        const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
        await expect(isCheckoutPage).to.be.true;
      });

      it('should fill guest personal information', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setPersonalInformation${index}`, baseContext);

        const isStepPersonalInfoCompleted = await foCheckoutPage.setGuestPersonalInformation(page, test.args.customer);
        await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
      });

      it('should fill address form and go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setAddressStep${index}`, baseContext);

        const isStepAddressComplete = await foCheckoutPage.setAddress(page, addressData);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should validate the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `validateOrder${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

        // Payment step - Choose payment step
        await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
        const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);

        // Check the confirmation message
        await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
      });
    });
  });

  // 2 - View order page
  describe('View order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should filter the Orders table by \'Customer\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', firstCustomerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, test.args.filterBy, 1);
      await expect(textColumn).to.contains(firstCustomerData.lastName);
    });

    it('should open the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 4 - Delete customers with bulk actions
  describe('Go to customers page', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });
  [
    {args: {customer: firstCustomerData}},
    {args: {customer: secondCustomerData}},
  ].forEach((test, index) => {
    describe(`Delete the customer ${test.args.customer.lastName}`, async () => {
      it('should filter list by lastName', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByLastname${index}`, baseContext);

        await customersPage.filterCustomers(
          page,
          'input',
          'lastname',
          test.args.customer.lastName,
        );

        const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'lastname');
        await expect(textResult).to.contains(test.args.customer.lastName);
      });

      it('should delete customer and check Result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index}`, baseContext);

        const deleteTextResult = await customersPage.deleteCustomer(page, 1);
        await expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetAfterDelete${index}`, baseContext);

        const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCustomersAfterReset).to.be.above(0);
      });
    });
  });
});
