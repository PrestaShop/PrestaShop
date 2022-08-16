require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const createAccountPage = require('@pages/FO/myAccount/add');

// Import common test
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import data
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_login_createAccount';

let browserContext;
let page;

const customerData = new CustomerFaker();

describe('FO - Login : Create account', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await homePage.goTo(page, global.FO.URL);

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await homePage.goToLoginPage(page);

    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should go to create account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

    await loginPage.goToCreateAccountPage(page);

    const pageHeaderTitle = await createAccountPage.getHeaderTitle(page);
    await expect(pageHeaderTitle).to.equal(createAccountPage.formTitle);
  });

  it('should create new account', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

    await createAccountPage.createAccount(page, customerData);

    const isCustomerConnected = await homePage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Created customer is not connected!').to.be.true;
  });

  it('should check if the page is redirected to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'isHomePage', baseContext);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage, 'Fail to redirect to FO home page!').to.be.true;
  });

  it('should sign out from FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

    await homePage.logout(page);

    const isCustomerConnected = await homePage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
