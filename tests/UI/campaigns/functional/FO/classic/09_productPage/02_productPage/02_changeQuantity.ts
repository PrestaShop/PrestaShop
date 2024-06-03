// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';
import {cartPage} from '@pages/FO/classic/cart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_changeQuantity';

/*
Scenario:
- Go to FO
- Go to the third product in the list
- Click up/down on quantity input
- Set quantity input (good/bad value)
 */
describe('FO - Product page : Change quantity', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should go to the third product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await homePage.goToProductPage(page, 3);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
  });

  it('should change the quantity by using the arrow \'UP\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity', baseContext);

    await productPage.setQuantityByArrowUpDown(page, 5, 'up');

    const productQuantity = await productPage.getProductQuantity(page);
    expect(productQuantity).to.equal(5);
  });

  it('should change the quantity by using the arrow \'Down\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity2', baseContext);

    await productPage.setQuantityByArrowUpDown(page, 1, 'down');

    const productQuantity = await productPage.getProductQuantity(page);
    expect(productQuantity).to.equal(1);
  });

  it('should add quantity of the product by setting input value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput', baseContext);

    await productPage.setQuantity(page, 12);
    await productPage.clickOnAddToCartButton(page);

    const isVisible = await blockCartModal.isBlockCartModalVisible(page);
    expect(isVisible).to.equal(true);
  });

  it('should click on continue shopping and check that the modal is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

    const isNotVisible = await blockCartModal.continueShopping(page);
    expect(isNotVisible).to.equal(true);
  });

  it('should check the cart notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

    const notificationsNumber = await productPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(12);
  });

  it('should set \'-24\' in the quantity input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput2', baseContext);

    await productPage.setQuantity(page, '-24');
    await productPage.clickOnAddToCartButton(page);

    const isVisible = await blockCartModal.isBlockCartModalVisible(page);
    expect(isVisible).to.equal(true);
  });

  it('should click on continue shopping and check that the modal is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

    const isNotVisible = await blockCartModal.continueShopping(page);
    expect(isNotVisible).to.equal(true);
  });

  it('should check the cart notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(13);
  });

  it('should set \'Prestashop\' in the quantity input and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput3', baseContext);

    await productPage.addProductToTheCart(page, 'Prestashop');

    const notificationsNumber = await homePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(14);
  });

  it('should remove product from shopping cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

    await cartPage.deleteProduct(page, 1);

    const notificationNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(0);
  });
});
