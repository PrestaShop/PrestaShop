require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orderSettings_enableGuestCheckout';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    orderSettingsPage: new OrderSettingsPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
  };
};

describe('Enable guest checkout', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Shop Parameters > Order Settings page
  loginCommon.loginBO();

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.orderSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', exist: false, pwdRequired: true}},
    {args: {action: 'enable', exist: true, pwdRequired: false}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.action} guest checkout`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}GuestCheckout`, baseContext);
      const result = await this.pageObjects.orderSettingsPage.setGuestCheckoutStatus(test.args.exist);
      await expect(result).to.contains(this.pageObjects.orderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AndViewMyShop`, baseContext);
      // Click on view my shop
      page = await this.pageObjects.orderSettingsPage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.homePage.changeLanguage('en');
      const isHomePage = await this.pageObjects.homePage.isHomePage();
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should verify the guest checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkGuestCheckout${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      // Check guest checkout
      const isNoticeVisible = await this.pageObjects.checkoutPage.isCreateAnAccountNoticeVisible();
      await expect(isNoticeVisible).to.be.equal(test.args.exist);
      const isPasswordRequired = await this.pageObjects.checkoutPage.isPasswordRequired();
      await expect(isPasswordRequired).to.be.equal(test.args.pwdRequired);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckAndBackToBO`, baseContext);
      page = await this.pageObjects.checkoutPage.closePage(browser, 1);
      this.pageObjects = await init();
      const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
    });
  });
});
