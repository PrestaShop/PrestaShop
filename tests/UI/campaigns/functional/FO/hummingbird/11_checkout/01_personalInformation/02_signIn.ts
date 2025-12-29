// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_checkout_personalInformation_signIn';

/*
Pre-condition:
- Install hummingbird them
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Enter an invalid credentials
- Login by default customer
- Logout
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Checkout - Personal information : Sign in', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const credentialsData: FakerCustomer = new FakerCustomer();

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Sign from Personal information step', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);
      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should proceed to checkout validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should enter an invalid credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, credentialsData);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);

      const loginError = await foHummingbirdCheckoutPage.getLoginError(page);
      expect(loginError).to.contains(foHummingbirdCheckoutPage.authenticationErrorMessage);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signIn', baseContext);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should click on edit Personal information step and get the identity of the customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerIdentity', baseContext);

      await foHummingbirdCheckoutPage.clickOnEditPersonalInformationStep(page);

      const customerIdentity = await foHummingbirdCheckoutPage.getCustomerIdentity(page);
      expect(customerIdentity).to.equal(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should check the existence of the text message \'If you sign out now, your cart will be emptied.\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const message = await foHummingbirdCheckoutPage.getLogoutMessage(page);
      expect(message).to.equal(foHummingbirdCheckoutPage.messageIfYouSignOut);
    });

    it('should logout and check that the customer is no longer connected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logout', baseContext);

      const isCustomerConnected = await foHummingbirdCheckoutPage.logOutCustomer(page);
      expect(isCustomerConnected, 'Customer is still connected').to.eq(false);
    });

    it('should check the message \'There are no more items in your cart\' in shopping cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemsNumber', baseContext);

      const message = await foHummingbirdCartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(foHummingbirdCartPage.noItemsInYourCartMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
