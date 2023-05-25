// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import cartPage from '@pages/FO/cart';
import {homePage} from '@pages/FO/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_cart_cart_changeQuantity';

describe('FO - cart : Change quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should add the first product to cart and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 1);
    await homePage.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.eq(cartPage.pageTitle);
  });

  it('should increase the product quantity by the touchspin up to 5', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'increaseQuantity5', baseContext);

    const quantity = await cartPage.setProductQuantity(page, 1, 5);
    await expect(quantity).to.eq(5);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(5);
  });

  it('should decrease the product quantity by the touchspin down to 2', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'decreaseQuantity2', baseContext);

    const quantity = await cartPage.setProductQuantity(page, 1, 2);
    await expect(quantity).to.eq(2);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(2);
  });

  it('should set the quantity 3 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity3', baseContext);

    await cartPage.editProductQuantity(page, 1, 3);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(3);
  });

  it('should set the quantity -6 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity-6', baseContext);

    await cartPage.editProductQuantity(page, 1, -6);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(3);
  });

  it('should set the quantity +6 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity+6', baseContext);

    await cartPage.editProductQuantity(page, 1, +6);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(6);
  });

  it('should set the quantity 64 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity64', baseContext);

    await cartPage.editProductQuantity(page, 1, 64);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(64);
  });

  it('should set the quantity 64 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity64', baseContext);

    await cartPage.editProductQuantity(page, 1, 'azerty');

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(64);
  });

  it('should set the quantity 2400 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity2400', baseContext);

    await cartPage.editProductQuantity(page, 1, 2400);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(2400);
  });

  it('should check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

    const alertText = await cartPage.getNotificationMessage(page);
    await expect(alertText).to.contains(cartPage.errorNotificationForProductQuantity);
  });

  it('should check that proceed to checkout button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProceedToCheckoutButton', baseContext);

    const isDisabled = await cartPage.isProceedToCheckoutButtonDisabled(page);
    await expect(isDisabled).to.be.true;
  });

  it('should set the quantity 0 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity', baseContext);

    await cartPage.editProductQuantity(page, 1, 0);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.eq(0);
  });
});
