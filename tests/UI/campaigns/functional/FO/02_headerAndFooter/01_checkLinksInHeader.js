require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const contactUsPage = require('@pages/FO/contactUs');
const cartPage = require('@pages/FO/cart');
const myAccountPage = require('@pages/FO/myAccount');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_checkLinksInHeader';

let browserContext;
let page;

/*
Go to FO
Check header links:
- Contact us
- Sign in
- My account( Customer name)
- Cart
- Sign out
- Logo
 */
describe('FO - Header and Footer : Check links in header page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check \'Contact us\' header link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContactUsHeaderLink', baseContext);

    // Check Contact us
    await homePage.clickOnHeaderLink(page, 'Contact us');

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle, 'Fail to open FO login page').to.contains(contactUsPage.pageTitle);
  });

  it('should check \'sign in\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignInLink', baseContext);

    // Check sign in link
    await homePage.clickOnHeaderLink(page, 'Sign in');

    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should sign in by default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

    // Sign in
    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
  });

  it('should check my account link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMyAccountLink', baseContext);

    await loginPage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should add a product to cart by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await loginPage.goToHomePage(page);

    // Add product to cart by quick view
    await homePage.addProductToCartByQuickView(page, 1, 3);

    // Close block cart modal
    const isQuickViewModalClosed = await homePage.closeBlockCartModal(page);
    await expect(isQuickViewModalClosed).to.be.true;
  });

  it('should check \'Cart\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartLink', baseContext);

    // Check cart link
    await homePage.clickOnHeaderLink(page, 'Cart');

    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should go to home page and check the notification number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber1', baseContext);

    await loginPage.goToHomePage(page);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    await expect(notificationsNumber, 'Notification number is not equal to 3!').to.be.equal(3);
  });

  it('should check \'Sign out\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignOutLink', baseContext);

    // Sign out
    await homePage.logout(page);

    const isCustomerConnected = await homePage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
  });

  it('should check that the cart is empty', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber2', baseContext);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    await expect(notificationsNumber, 'The cart is not empty!').to.be.equal(0);
  });

  it('should check \'Logo\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

    await homePage.clickOnHeaderLink(page, 'Logo');

    const pageTitle = await homePage.getPageTitle(page);
    await expect(pageTitle).to.equal(homePage.pageTitle);
  });
});
