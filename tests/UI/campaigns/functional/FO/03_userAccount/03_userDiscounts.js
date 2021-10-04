require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
// BO
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// FO
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foVouchersPage = require('@pages/FO/myAccount/vouchers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_userDiscounts';

let browserContext;
let page;


const today = new Date();
// Create a previous date for cart rules (yyyy-mm-dd)
today.setFullYear(today.getFullYear() - 1);
const previousDate = today.toISOString().slice(0, 10);

// Create customer data
const CustomerFaker = require('@data/faker/customer');

const customerData = new CustomerFaker();

// Create cart rule data
const CartRuleFaker = require('@data/faker/cartRule');

const firstCartRule = new CartRuleFaker(
  {
    code: 'customerDataFirstCartRule',
    customer: customerData.email,
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: previousDate,
  },
);

const secondCartRule = new CartRuleFaker(
  {
    code: 'customerDataSecondCartRule',
    customer: customerData.email,
    freeShipping: true,
    dateFrom: previousDate,
  },
);

const createdCartRules = [firstCartRule, secondCartRule];

/*
Created 2 cart rules for customer in BO
Go To FO
Sign in
Check cart rules in account page
Go Back to BO and delete cart rules
 */
describe('FO - Account : View vouchersFO - Account  account page', async () => {
  // before and after functions
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

  describe('Create customer in BO', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToCreate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, customerData);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });

  describe('Create 2 cart rules', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    createdCartRules.forEach((cartRule, index) => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRule);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });
  });

  describe('Verify created cart rules for customer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foHomePage.goToLoginPage(page);
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

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToVouchersPage(page);
      const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
    });

    createdCartRules.forEach((cartRule, index) => {
      it(`should check the existence of the created cart code ${cartRule.code}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

        const cartRuleCodeFromTable = await foVouchersPage.getVoucherCodeFromTable(page, index + 1);
        await expect(cartRuleCodeFromTable).to.equal(cartRule.code);
      });
    });
  });

  describe('Delete cart rules', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });


    it('should delete cart rules with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });
  });

  describe('Delete the created customer', async () => {
    it('should go customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textResult).to.contains(customerData.email);
    });

    it('should delete customer and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const deleteTextResult = await customersPage.deleteCustomer(page, 1);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
    });
  });
});
