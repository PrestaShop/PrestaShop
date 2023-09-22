// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/cart';
import {contactUsPage} from '@pages/FO/contactUs';
import {homePage} from '@pages/FO/home';
import {loginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_headerAndFooter_checkLinksInHeader';

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
  let browserContext: BrowserContext;
  let page: Page;

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
    expect(isHomePage).to.eq(true);
  });

  it('should check \'Contact us\' header link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContactUsHeaderLink', baseContext);

    // Check Contact us
    await homePage.clickOnHeaderLink(page, 'Contact us');

    const pageTitle = await contactUsPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').to.contains(contactUsPage.pageTitle);
  });

  it('should check \'sign in\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignInLink', baseContext);

    // Check sign in link
    await homePage.clickOnHeaderLink(page, 'Sign in');

    const pageTitle = await loginPage.getPageTitle(page);
    expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should sign in by default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

    // Sign in
    await loginPage.customerLogin(page, Customers.johnDoe);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
  });

  it('should check my account link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMyAccountLink', baseContext);

    await loginPage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should add a product to cart by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await loginPage.goToHomePage(page);
    // Add product to cart by quick view
    await homePage.addProductToCartByQuickView(page, 1, 3);

    // Close block cart modal
    const isQuickViewModalClosed = await homePage.closeBlockCartModal(page);
    expect(isQuickViewModalClosed).to.eq(true);
  });

  it('should check \'Cart\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartLink', baseContext);

    // Check cart link
    await homePage.clickOnHeaderLink(page, 'Cart');

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should go to home page and check the notification number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber1', baseContext);

    await loginPage.goToHomePage(page);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    expect(notificationsNumber, 'Notification number is not equal to 3!').to.be.equal(3);
  });

  it('should check \'Sign out\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignOutLink', baseContext);

    // Sign out
    await homePage.logout(page);

    const isCustomerConnected = await homePage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
  });

  it('should check that the cart is empty', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber2', baseContext);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    expect(notificationsNumber, 'The cart is not empty!').to.be.equal(0);
  });

  it('should check \'Logo\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

    await homePage.clickOnHeaderLink(page, 'Logo', false);

    const pageTitle = await homePage.getPageTitle(page);
    expect(pageTitle).to.equal(homePage.pageTitle);
  });
});
