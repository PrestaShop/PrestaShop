require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_login_logout';

let browserContext;
let page;

describe('FO - Login : Logout from FO', async () => {
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

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO1', baseContext);

    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should logout by the link in the header', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

    await homePage.logout(page);

    const isCustomerConnected = await homePage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO2', baseContext);

    await homePage.goToLoginPage(page);
    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await homePage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should logout by the link in the footer of account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByFooterLink', baseContext);

    await myAccountPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
  });
});
