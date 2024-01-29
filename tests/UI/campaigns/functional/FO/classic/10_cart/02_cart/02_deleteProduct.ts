// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {homePage as foHomePage, homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_cart_cart_deleteProduct';

describe('FO - cart : Delete product', async () => {
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
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to cart and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 1);
    await homePage.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.eq(cartPage.pageTitle);
  });

  it('should set the quantity 0 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0', baseContext);

    await cartPage.editProductQuantity(page, 1, 0);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(0);
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    await cartPage.goToHomePage(page);

    const result = await foHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should add the first product to cart and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart1', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 1);
    await homePage.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.eq(cartPage.pageTitle);
  });

  it('should delete the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    await cartPage.deleteProduct(page, 1);

    const notificationNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.be.equal(0);
  });
});
