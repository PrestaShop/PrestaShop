require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_login_login';

let browserContext;
let page;

const firstCredentialsData = new CustomerFaker();
const secondCredentialsData = new CustomerFaker({password: DefaultCustomer.password});
const thirdCredentialsData = new CustomerFaker({email: DefaultCustomer.email});

describe('FO - Login : Login in FO', async () => {
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

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await loginPage.customerLogin(page, firstCredentialsData, false);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

    await loginPage.customerLogin(page, secondCredentialsData, false);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

    await loginPage.customerLogin(page, thirdCredentialsData, false);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should check password type', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordType', baseContext);

    const inputType = await loginPage.getPasswordType(page);
    await expect(inputType).to.equal('password');
  });

  it('should click on show button and check the password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnShowButton', baseContext);

    const inputType = await loginPage.showPassword(page);
    await expect(inputType).to.equal('text');
  });

  it('should enter a valid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });
});
