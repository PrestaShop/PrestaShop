// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import homePage from '@pages/FO/home';

require('module-alias/register');

const {expect} = require('chai');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_FO_userAccount_logOut';

let browserContext;
let page;

describe('FO - User Account : LogOut', async () => {
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

  it('should logIn', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await homePage.goToLoginPage(page);
    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await homePage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should logOut with link in the footer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutWithLinkAtAccountPage', baseContext);

    await myAccountPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
  });
});
