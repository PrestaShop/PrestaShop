// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_cart_modal_displayModal';

describe('FO - cart : Display modal when adding a product to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to cart by quick view and click on continue button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    const successMessage = await homePage.addProductToCartByQuickView(page, 1, 2);
    expect(successMessage).to.contains(homePage.successAddToCartMessage);

    const isModalNotVisible = await homePage.continueShopping(page);
    expect(isModalNotVisible).to.eq(true);
  });

  it('should go to the second product page and add the product to the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductPage', baseContext);

    await homePage.goToProductPage(page, 2);
    // Add the product to the cart
    await productPage.addProductToTheCart(page, 3);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.eq(cartPage.pageTitle);
  });

  it('should check notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.eq(5);
  });
});
