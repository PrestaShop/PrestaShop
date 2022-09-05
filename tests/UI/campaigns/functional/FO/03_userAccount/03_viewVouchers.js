require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const {getDateFormat} = require('@utils/date');
const testContext = require('@utils/testContext');

// Import common tests
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {createCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foVouchersPage = require('@pages/FO/myAccount/vouchers');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const CartRuleFaker = require('@data/faker/cartRule');

const baseContext = 'functional_FO_userAccount_viewVouchers';

let browserContext;
let page;

// Data to create a date format
const pastDate = getDateFormat('yyyy-mm-dd', 'past');
const futureDate = getDateFormat('yyyy-mm-dd', 'future');
const expirationDate = getDateFormat('mm/dd/yyyy', 'future');

const customerData = new CustomerFaker({});

// Data to create 2 cart rules
const firstCartRule = new CartRuleFaker(
  {
    code: 'promo20',
    customer: customerData.email,
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: pastDate,
    dateTo: futureDate,
  },
);
const secondCartRule = new CartRuleFaker(
  {
    code: 'freeShipping',
    customer: customerData.email,
    freeShipping: true,
    dateFrom: pastDate,
    dateTo: futureDate,
  },
);

/*
Pre-condition:
- Create customer
- Create 2 cart rules for the customer
Scenario:
- Go To FO and sign in
- Check vouchers in account page
Post-condition:
- Delete customer
 */
describe('FO - Account : View vouchers', async () => {
  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create 2 cart rules for the created customer
  [firstCartRule, secondCartRule].forEach((cartRule, index) => {
    createCartRuleTest(cartRule, `${baseContext}_preTest_${index + 2}`);
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('View vouchers on FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, customerData);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to vouchers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await foMyAccountPage.goToVouchersPage(page);
      const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
    });

    [
      {args: {column: 'code', row: 1, value: firstCartRule.code}},
      {args: {column: 'description', row: 1, value: firstCartRule.name}},
      {args: {column: 'quantity', row: 1, value: '1'}},
      {args: {column: 'value', row: 1, value: '20.00%'}},
      {args: {column: 'minimum', row: 1, value: 'None'}},
      {args: {column: 'cumulative', row: 1, value: 'Yes'}},
      {args: {column: 'expiration_date', row: 1, value: expirationDate}},
      {args: {column: 'code', row: 2, value: secondCartRule.code}},
      {args: {column: 'description', row: 2, value: secondCartRule.name}},
      {args: {column: 'quantity', row: 2, value: '1'}},
      {args: {column: 'value', row: 2, value: 'Free shipping'}},
      {args: {column: 'minimum', row: 2, value: 'None'}},
      {args: {column: 'cumulative', row: 2, value: 'Yes'}},
      {args: {column: 'expiration_date', row: 2, value: expirationDate}},
    ].forEach((cartRule, index) => {
      it(`should check the voucher ${cartRule.args.column} n°${cartRule.args.row}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

        const cartRuleTextColumn = await foVouchersPage.getTextColumnFromTableVouchers(
          page,
          cartRule.args.row,
          cartRule.args.column,
        );
        await expect(cartRuleTextColumn).to.equal(cartRule.args.value);
      });
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
