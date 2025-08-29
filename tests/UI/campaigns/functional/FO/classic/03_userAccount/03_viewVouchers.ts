import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';

import {
  type BrowserContext,
  FakerCartRule,
  FakerCustomer,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyVouchersPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_viewVouchers';

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
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create a date format
  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const expirationDate: string = utilsDate.getDateFormat('mm/dd/yyyy', 'future');
  const customerData: FakerCustomer = new FakerCustomer({});
  const firstCartRule: FakerCartRule = new FakerCartRule({
    code: 'promo20',
    customer: customerData,
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: pastDate,
    dateTo: futureDate,
  });
  const secondCartRule: FakerCartRule = new FakerCartRule({
    code: 'freeShipping',
    customer: customerData,
    freeShipping: true,
    dateFrom: pastDate,
    dateTo: futureDate,
  });

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create 2 cart rules for the created customer
  [firstCartRule, secondCartRule].forEach((cartRule: FakerCartRule, index: number) => {
    createCartRuleTest(cartRule, `${baseContext}_preTest_${index + 2}`);
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('View vouchers on FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, customerData);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to vouchers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToVouchersPage(page);

      const pageHeaderTitle = await foClassicMyVouchersPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyVouchersPage.pageTitle);
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

        const cartRuleTextColumn = await foClassicMyVouchersPage.getTextColumnFromTableVouchers(
          page,
          cartRule.args.row,
          cartRule.args.column,
        );
        expect(cartRuleTextColumn).to.equal(cartRule.args.value);
      });
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
