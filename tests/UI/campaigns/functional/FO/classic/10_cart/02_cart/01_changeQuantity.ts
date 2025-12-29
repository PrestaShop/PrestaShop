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

const baseContext: string = 'functional_FO_classic_cart_cart_changeQuantity';

describe('FO - Cart : Change quantity', async () => {
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

  it('should increase the product quantity by the touchspin up to 5', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'increaseQuantity5', baseContext);

    const quantity = await foClassicCartPage.setProductQuantity(page, 1, 5);
    expect(quantity).to.eq(5);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(5);
  });

  it('should decrease the product quantity by the touchspin down to 2', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'decreaseQuantity2', baseContext);

    const quantity = await foClassicCartPage.setProductQuantity(page, 1, 2);
    expect(quantity).to.eq(2);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(2);
  });

  it('should set the quantity 3 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity3', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 3);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(3);
  });

  it('should set the quantity -6 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity-6', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, -6);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(3);
  });

  it('should set the quantity +6 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity+6', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, +6);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(6);
  });

  it('should set the quantity 64 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity64', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 64);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(64);
  });

  it('should set \'azerty\' in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAZERTY', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 'azerty');

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(64);
  });

  it('should set the quantity 2400 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity2400', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 2400);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(2400);
  });

  it('should check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

    const alertText = await foClassicCartPage.getNotificationMessage(page);
    expect(alertText).to.contains(foClassicCartPage.errorNotificationForProductQuantity(300));
  });

  it('should check that proceed to checkout button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProceedToCheckoutButton', baseContext);

    const isDisabled = await foClassicCartPage.isProceedToCheckoutButtonDisabled(page);
    expect(isDisabled).to.eq(true);
  });

  it('should set the quantity 0 in the input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setQuantity', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 0);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.eq(0);
  });
});
