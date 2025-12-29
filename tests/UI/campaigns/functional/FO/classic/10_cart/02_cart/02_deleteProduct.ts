import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  foClassicCartPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_cart_cart_deleteProduct';

describe('FO - Cart : Delete product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to cart and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);
    await foClassicModalQuickViewPage.addToCartByQuickView(page);
    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
  });

  it('should set the quantity 0 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 0);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(0);
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    await foClassicCartPage.goToHomePage(page);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should add the first product to cart and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart1', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);
    await foClassicModalQuickViewPage.addToCartByQuickView(page);
    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
  });

  it('should delete the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    await foClassicCartPage.deleteProduct(page, 1);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.be.equal(0);
  });
});
