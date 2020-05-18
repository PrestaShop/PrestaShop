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
const FOLoginPage = require('@pages/FO/login');
const FOBasePage = require('@pages/FO/FObasePage');
const CustomerFaker = require('@data/faker/customer');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_CRUDCustomerInBO';

let browser;
let page;
let numberOfCustomers = 0;
let createCustomerData;
let editCustomerData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
    viewCustomerPage: new ViewCustomerPage(page),
    foLoginPage: new FOLoginPage(page),
    foBasePage: new FOBasePage(page),
  };
};

// Create, Read, Update and Delete Customer in BO
describe('Create, Read, Update and Delete Customer in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createCustomerData = await (new CustomerFaker());
    editCustomerData = await (new CustomerFaker({enabled: false}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to customers page', async function () {
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
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Create customer and go to FO to check sign in is OK
  describe('Create Customer in BO and check Sign in in FO', async () => {
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

    it('should go to FO and check sign in with new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFOSignInWithCreatedCustomer', baseContext);
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.foLoginPage.customerLogin(createCustomerData);
      const isCustomerConnected = await this.pageObjects.foBasePage.isCustomerConnected();
      await expect(isCustomerConnected).to.be.true;
      await this.pageObjects.foBasePage.logout();
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  // 2 : View Customer and check Creation data are correct
  describe('View Customer Created', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getPersonalInformationTitle();
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });
  });
  // 3 : Update customer and check that customer can't sign in in BO (enabled = false)
  describe('Update Customer Created', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPageToUpdate', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);
      await this.pageObjects.customersPage.goToEditCustomerPage('1');
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleEdit);
    });

    it('should update customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(editCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulUpdateMessage);
      const numberOfCustomersAfterUpdate = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterUpdate).to.be.equal(numberOfCustomers + 1);
    });

    it('should go to FO and check sign in with edited account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFOSignInWithUpdatedCustomer', baseContext);
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.foLoginPage.customerLogin(editCustomerData);
      const isCustomerConnected = await this.pageObjects.foBasePage.isCustomerConnected();
      await expect(isCustomerConnected).to.be.false;
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  // 4 : View customer and check data are correct
  describe('View Customer Updated', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedCustomer', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getPersonalInformationTitle();
      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
    });
  });
  // 5 : Delete Customer from BO
  describe('Delete Customer', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goTocustomersPageToDelete', baseContext);
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
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);
      const textResult = await this.pageObjects.customersPage.deleteCustomer('1');
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulDeleteMessage);
      const numberOfCustomersAfterDelete = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
