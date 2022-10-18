require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAddressesPage = require('@pages/FO/myAccount/addresses');
const foAddAddressesPage = require('@pages/FO/myAccount/addAddress');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/viewOrderBasePage');
// const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const orderPageProductsBlock = require('@pages/BO/orders/view/tabListBlock');
// /var/www/html/PrestaShop/tests/UI/pages/BO/orders/view/tabListBlock.js
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let numberOfCreditSlips = 1;
const todayDate = getDateFormat('yyyy-mm-dd');
const todayDateToCheck = getDateFormat('mm/dd/yyyy');
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition n°1:
- Create 7 orders
Pre-condition n°3:
- Change the status of 11 orders and create partial refund
Scenario:
- Create 2 credit slips for the same order
- Filter Credit slips table( by ID, Order ID, Date issued From and To)
- Download the 2 credit slip files and check them
 */
describe('BO - Orders - Credit Slips - Sort & Pagination Credit Slips', async () => {
  // Pre-condition: Create 7 order in FO
  for (let i = 0; i < numberOfCreditSlips; i++) {
    createOrderByCustomerTest(orderByCustomerData, baseContext);
  }

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create 2 credit slips for the same order', async () => {
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

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);
      
      // const result = await orderPageTabListBlock.modifyOrderStatus(page, test.args.status);
      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    // it(`should create the partial refund n°`, async function () {
    //   await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund`, baseContext);

    //   await orderPageTabListBlock.clickOnPartialRefund(page);

    //   const textMessage = await orderPageProductsBlock.addPartialRefundProduct(
    //     page,
    //     test.args.productID,
    //     test.args.quantity,
    //   );
    //   await expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
    // });

  });

});
