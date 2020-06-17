/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_payment_preferences_countryRestrictions';

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PreferencesPage = require('@pages/BO/payment/preferences');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');

// Import data
const {DefaultAccount} = require('@data/demo/customer');

let browserContext;
let page;

const countryID = 74;

// Init objects needed
const init = async function () {
  return {
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

describe('Configure country restrictions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to Payment > Preferences page
  loginCommon.loginBO();

  it('should go to \'Payment > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.paymentParentLink,
      this.pageObjects.dashboardPage.preferencesLink,
    );

    await this.pageObjects.preferencesPage.closeSfToolBar();

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
    it(`should ${test.args.action} the France country for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await this.pageObjects.preferencesPage.setCountryRestriction(
        countryID,
        test.args.paymentModule,
        test.args.exist,
      );

      await expect(result).to.contains(this.pageObjects.preferencesPage.successfulUpdateMessage);
    });

    it('should go to FO and add the first product to the cart', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `check_${test.args.paymentModule}_${test.args.exist}`,
        baseContext,
      );

      // Click on view my shop
      page = await this.pageObjects.preferencesPage.viewMyShop();
      this.pageObjects = await init();

      // Change language in FO
      await this.pageObjects.foBasePage.changeLanguage('en');

      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);

      // Add the product to the cart
      await this.pageObjects.productPage.addProductToTheCart();

      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();

      const isCheckoutPage = await this.pageObjects.checkoutPage.isCheckoutPage();
      await expect(isCheckoutPage).to.be.true;
    });

    // Personal information step - Login
    it('should login and go to address step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `loginToFO${index}`,
        baseContext,
      );

      if (index === 0) {
        // Personal information step - Login
        await this.pageObjects.checkoutPage.clickOnSignIn();
        const isStepLoginComplete = await this.pageObjects.checkoutPage.customerLogin(DefaultAccount);
        await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
      }
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToDeliveryStep${index}`,
        baseContext,
      );

      // Address step - Go to delivery step
      const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should continue to payment step and check the existence of payment method', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToPaymentStep${index}`,
        baseContext,
      );

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Check payment method
      const isVisible = await this.pageObjects.checkoutPage.isPaymentMethodExist(test.args.paymentModule);
      await expect(isVisible).to.be.equal(test.args.exist);

      // Go back to BO
      page = await this.pageObjects.checkoutPage.closePage(browserContext, 0);
      this.pageObjects = await init();
    });
  });
});
