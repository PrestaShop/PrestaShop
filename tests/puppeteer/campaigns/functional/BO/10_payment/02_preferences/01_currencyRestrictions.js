require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_payment_preferences_currencyRestrictions';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PreferencesPage = require('@pages/BO/payment/preferences');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
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
    preferencesPage: new PreferencesPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
  };
};

describe('Configure currency restrictions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Payment > Preferences page
  loginCommon.loginBO();

  it('should go to \'Payment > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.paymentParentLink,
      this.pageObjects.boBasePage.preferencesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.preferencesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.preferencesPage.pageTitle);
  });

  const tests = [
    {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
    {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
  ];
  tests.forEach((test, index) => {
    it(`should ${test.args.action} the euro currency for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);
      const result = await this.pageObjects.preferencesPage.setCurrencyRestriction(
        test.args.paymentModule,
        test.args.exist,
      );
      await expect(result).to.contains(this.pageObjects.preferencesPage.successfulUpdateMessage);
    });

    it(`should go to FO and check the '${test.args.paymentModule}' payment module`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `check_${test.args.paymentModule}_${test.args.exist}`,
        baseContext,
      );
      // Click on view my shop
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await this.pageObjects.checkoutPage.clickOnSignIn();
        await this.pageObjects.checkoutPage.customerLogin(DefaultAccount);
      }
      // Address step - Go to delivery step
      const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      // Payment step - Choose payment step
      const isVisible = await this.pageObjects.checkoutPage.isPaymentMethodExist(test.args.paymentModule);
      await expect(isVisible).to.be.equal(test.args.exist);
      page = await this.pageObjects.checkoutPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
});
