// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import createAccountTest from '@commonTests/FO/hummingbird/account';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import foLoginPage from '@pages/FO/hummingbird/login';
import myAccountPage from '@pages/FO/hummingbird/myAccount';
import foVouchersPage from '@pages/FO/hummingbird/myAccount/vouchers';

// Import data
import CartRuleData from '@data/faker/cartRule';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_userAccount_viewVouchers';

/*
Pre-condition:
- Create customer
- Create 2 cart rules for the customer
- Install hummingbird theme
Scenario:
- Go To FO and sign in
- Check vouchers in account page
Post-condition:
- Delete customer
- Uninstall hummingbird theme
 */
describe('FO - Account : View vouchers', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create a date format
  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');
  const expirationDate: string = date.getDateFormat('mm/dd/yyyy', 'future');
  const customerData: FakerCustomer = new FakerCustomer({});
  const firstCartRule: CartRuleData = new CartRuleData({
    code: 'promo20',
    customer: customerData,
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: pastDate,
    dateTo: futureDate,
  });
  const secondCartRule: CartRuleData = new CartRuleData({
    code: 'freeShipping',
    customer: customerData,
    freeShipping: true,
    dateFrom: pastDate,
    dateTo: futureDate,
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_0`);

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create 2 cart rules for the created customer
  [firstCartRule, secondCartRule].forEach((cartRule: CartRuleData, index: number) => {
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
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, customerData);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to vouchers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToVouchersPage(page);

      const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
    });

    [
      {args: {column: 'code', row: 1, value: firstCartRule.code}},
      {args: {column: 'description', row: 1, value: firstCartRule.name}},
      {args: {column: 'quantity', row: 1, value: '1'}},
      {args: {column: 'value', row: 1, value: '20%'}},
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
    ].forEach((cartRule, index: number) => {
      it(`should check the voucher ${cartRule.args.column} n°${cartRule.args.row}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

        const cartRuleTextColumn = await foVouchersPage.getTextColumnFromTableVouchers(
          page,
          cartRule.args.row,
          cartRule.args.column,
        );
        expect(cartRuleTextColumn).to.equal(cartRule.args.value);
      });
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
