import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  foClassicCartPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_cart_modal_continueShopping';

describe('FO - Cart - Modal : Continue shopping / Proceed to checkout / Close', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to cart by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);
    await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);

    const isBlockCartModal = await foClassicModalBlockCartPage.isBlockCartModalVisible(page);
    expect(isBlockCartModal).to.equal(true);

    const successMessage = await foClassicModalBlockCartPage.getBlockCartModalTitle(page);
    expect(successMessage).to.contains(foClassicHomePage.successAddToCartMessage);
  });

  it('should click on continue shopping button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

    const isModalNotVisible = await foClassicModalBlockCartPage.continueShopping(page);
    expect(isModalNotVisible).to.equal(true);
  });

  it('should go to the second product page and add the product to the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 2);
    await foClassicProductPage.clickOnAddToCartButton(page);

    const successMessage = await foClassicModalBlockCartPage.getBlockCartModalTitle(page);
    expect(successMessage).to.contains(foClassicHomePage.successAddToCartMessage);
  });

  it('should close the blockCart modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal', baseContext);

    const isQuickViewModalClosed = await foClassicModalBlockCartPage.closeBlockCartModal(page);
    expect(isQuickViewModalClosed).to.equal(true);
  });

  it('should click on add product to cart button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicProductPage.clickOnAddToCartButton(page);

    const successMessage = await foClassicModalBlockCartPage.getBlockCartModalTitle(page);
    expect(successMessage).to.contains(foClassicHomePage.successAddToCartMessage);
  });

  it('should close the blockCart modal by clicking outside the modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal2', baseContext);

    const isQuickViewModalClosed = await foClassicModalBlockCartPage.closeBlockCartModal(page, true);
    expect(isQuickViewModalClosed).to.equal(true);
  });

  it('should click on add product to cart button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

    await foClassicProductPage.clickOnAddToCartButton(page);

    const successMessage = await foClassicModalBlockCartPage.getBlockCartModalTitle(page);
    expect(successMessage).to.contains(foClassicHomePage.successAddToCartMessage);
  });

  it('should click on proceed to checkout button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(5);
  });

  it('should delete the shopping cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProducts', baseContext);

    await foClassicCartPage.deleteProduct(page, 2);
    await foClassicCartPage.deleteProduct(page, 1);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.be.equal(0);
  });
});
