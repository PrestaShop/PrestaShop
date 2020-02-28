require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerSettings_customer_enableDisableRedisplayCartAtLogin';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const HomePage = require('@pages/FO/home');
const FOBasePage = require('@pages/FO/FObasePage');
const LoginFOPage = require('@pages/FO/login');
// Importing data
const {DefaultAccount} = require('@data/demo/customer');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customerSettingsPage: new CustomerSettingsPage(page),
    homePage: new HomePage(page),
    foBasePage: new FOBasePage(page),
    loginFOPage: new LoginFOPage(page),
  };
};

/*
Enable re-display cart at login
Login FO and add a product to the cart
Logout FO then Login and check that the cart is not empty
Disable re-display cart at login
 */
describe('Enable/Disable re-display cart at login', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to customer settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.customerSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.customerSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customerSettingsPage.pageTitle);
  });

  it('should enable re-display cart at login', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableRedisplayCartAtLogin', baseContext);
    const result = await this.pageObjects.customerSettingsPage.setRedisplayCartAtLogin(true);
    await expect(result).to.contains(this.pageObjects.customerSettingsPage.successfulUpdateMessage);
  });

  it('should login FO and add the first product to the cart then logout ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToTheCart', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    // Login FO
    await this.pageObjects.homePage.goToLoginPage();
    await this.pageObjects.loginFOPage.customerLogin(DefaultAccount);
    const connected = await this.pageObjects.homePage.isCustomerConnected();
    await expect(connected, 'Customer is not connected in FO').to.be.true;
    // Add first product to the cart
    await this.pageObjects.homePage.goToHomePage();
    await this.pageObjects.homePage.addProductToCartByQuickView(1, 1);
    await this.pageObjects.homePage.proceedToCheckout();
    const notificationsNumber = await this.pageObjects.homePage.getCartNotificationsNumber();
    await expect(notificationsNumber).to.be.above(0);
    // Logout from FO
    await this.pageObjects.foBasePage.logout();
  });

  it('should login FO and check that the cart is not empty', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFOAndCheckNotificationNumber', baseContext);
    // Login FO
    await this.pageObjects.homePage.goToLoginPage();
    await this.pageObjects.loginFOPage.customerLogin(DefaultAccount);
    const connected = await this.pageObjects.homePage.isCustomerConnected();
    await expect(connected, 'Customer is not connected in FO').to.be.true;
    const notificationsNumber = await this.pageObjects.homePage.getCartNotificationsNumber();
    await expect(notificationsNumber).to.be.above(0);
    // Logout from FO
    await this.pageObjects.foBasePage.logout();
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should disable re-display cart at login', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableRedisplayCartAtLogin', baseContext);
    const result = await this.pageObjects.customerSettingsPage.setRedisplayCartAtLogin(false);
    await expect(result).to.contains(this.pageObjects.customerSettingsPage.successfulUpdateMessage);
  });
});
