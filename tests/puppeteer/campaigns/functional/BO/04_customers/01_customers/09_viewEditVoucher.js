require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const AddCustomerPage = require('@pages/BO/customers/add');
const ViewCustomerPage = require('@pages/BO/customers/view');
const AddAddressPage = require('@pages/BO/customers/addresses/add');
const ViewOrderPage = require('@pages/BO/orders/view');
const CartRulesPage = require('@pages/BO/catalog/discounts');
const AddCartRulePage = require('@pages/BO/catalog/discounts/add');
// Importing data
const CustomerFaker = require('@data/faker/customer');
const CartRuleFaker = require('@data/faker/cartRule');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_viewEditVoucher';

let browser;
let page;
let numberOfCustomers = 0;
const newVoucher = 'RIJ5NG2H';
const editVoucher = 'NM74LK91';
const createCustomerData = new CustomerFaker({defaultCustomerGroup: 'Customer'});
const cartRuleData = new CartRuleFaker({customer: createCustomerData.email, code: newVoucher});
const editCartRuleData = new CartRuleFaker({customer: createCustomerData.email, code: editVoucher});


// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
    addAddressPage: new AddAddressPage(page),
    viewCustomerPage: new ViewCustomerPage(page),
    viewOrderPage: new ViewOrderPage(page),
    cartRulesPage: new CartRulesPage(page),
    addCartRulePage: new AddCartRulePage(page),
  };
};

// Create edit voucher from view customer page
describe('Create/Edit and view voucher from customer information page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Create customer
  describe('Create customer in BO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);
      await this.pageObjects.customersPage.goToAddNewCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(createCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);
      const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberOfElementInGrid();
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });
  // 2 : Create voucher
  describe('Create voucher', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.discountsLink,
      );
      const pageTitle = await this.pageObjects.cartRulesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.cartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);
      await this.pageObjects.cartRulesPage.goToNewCartRulePage();
      const pageTitle = await this.pageObjects.addCartRulePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCartRulePage.pageTitle);
    });

    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewCartRule', baseContext);
      const textResult = await this.pageObjects.addCartRulePage.createEditCartRules(cartRuleData);
      await expect(textResult).to.contains(this.pageObjects.addCartRulePage.successfulCreationMessage);
    });
  });
  // 3 : View customer after creating the voucher
  describe('View customer after creating the voucher', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateOrder', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage(1);
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check vouchers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchers', baseContext);
      const vouchers = await this.pageObjects.viewCustomerPage.getTextFromElement('Vouchers');
      expect(vouchers).to.contains(newVoucher);
      expect(vouchers).to.contains(cartRuleData.name);
    });
  });
  // 4 : Edit voucher
  describe('Edit voucher', async () => {
    it('should go to edit voucher page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditVoucherPage', baseContext);
      await this.pageObjects.viewCustomerPage.goToEditVoucherPage();
      const pageTitle = await this.pageObjects.addCartRulePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCartRulePage.pageTitle);
    });

    it('should update the created cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);
      const textResult = await this.pageObjects.addCartRulePage.createEditCartRules(editCartRuleData);
      await expect(textResult).to.contains(this.pageObjects.addCartRulePage.successfulUpdateMessage);
    });
  });
  // 5 : View customer after editing the voucher
  describe('View customer after editing the voucher', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateOrder', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage(1);
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check vouchers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchers', baseContext);
      const vouchers = await this.pageObjects.viewCustomerPage.getTextFromElement('Vouchers');
      expect(vouchers).to.contains(editVoucher);
      expect(vouchers).to.contains(editCartRuleData.name);
    });
  });
  // 4 : Delete customer from BO
  describe('Delete Customer', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);
      const textResult = await this.pageObjects.customersPage.deleteCustomer(1);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulDeleteMessage);
      const numberOfCustomersAfterDelete = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
