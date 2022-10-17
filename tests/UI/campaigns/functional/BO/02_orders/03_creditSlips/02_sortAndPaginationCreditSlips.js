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
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
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

let numberOfCreditSlips = 7;
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
Scenario:
- Create 2 credit slips for the same order
- Filter Credit slips table( by ID, Order ID, Date issued From and To)
- Download the 2 credit slip files and check them
 */
describe('BO - Orders - Credit Slips - Sort & Pagination Credit Slips', async () => {
  // Pre-condition: Create 7 order in FO
  for (let i = 0; i < numberOfCreditSlips; i++) {

    createOrderByCustomerTest(orderByCustomerData, baseContext);
    console(i)
  }

});
