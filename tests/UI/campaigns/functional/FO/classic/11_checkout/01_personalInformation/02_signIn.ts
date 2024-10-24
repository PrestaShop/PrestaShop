// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_classic_checkout_personalInformation_signIn';

/*
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Enter an invalid credentials
- Login by default customer
- Logout
 */
describe('FO - Checkout - Personal information : Sign in', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const credentialsData: FakerCustomer = new FakerCustomer();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicHomePage.goToProductPage(page, 1);
    await foClassicProductPage.addProductToTheCart(page, 1);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout validate the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await foClassicCheckoutPage.clickOnSignIn(page);

    const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, credentialsData);
    expect(isCustomerConnected, 'Customer is connected').to.eq(false);

    const loginError = await foClassicCheckoutPage.getLoginError(page);
    expect(loginError).to.contains(foClassicCheckoutPage.authenticationErrorMessage);
  });

  it('should sign in with customer credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signIn', baseContext);

    const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should click on edit Personal information step and get the identity of the customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerIdentity', baseContext);

    await foClassicCheckoutPage.clickOnEditPersonalInformationStep(page);

    const customerIdentity = await foClassicCheckoutPage.getCustomerIdentity(page);
    expect(customerIdentity).to.equal(`Connected as ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}.`);
  });

  it('should check the existence of the text message \'If you sign out now, your cart will be emptied.\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

    const message = await foClassicCheckoutPage.getLogoutMessage(page);
    expect(message).to.equal(foClassicCheckoutPage.messageIfYouSignOut);
  });

  it('should logout and check that the customer is no longer connected', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logout', baseContext);

    const isCustomerConnected = await foClassicCheckoutPage.logOutCustomer(page);
    expect(isCustomerConnected, 'Customer is still connected').to.eq(false);
  });

  it('should check the message \'There are no more items in your cart\' in shopping cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemsNumber', baseContext);

    const message = await foClassicCartPage.getNoItemsInYourCartMessage(page);
    expect(message).to.equal(foClassicCartPage.noItemsInYourCartMessage);
  });
});
