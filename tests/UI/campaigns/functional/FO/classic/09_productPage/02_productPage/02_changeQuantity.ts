import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataProducts,
  foClassicCartPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should go to the third product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 3);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
  });

  it('should change the quantity by using the arrow \'UP\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity', baseContext);

    await foClassicProductPage.setQuantityByArrowUpDown(page, 5, 'up');

    const productQuantity = await foClassicProductPage.getProductQuantity(page);
    expect(productQuantity).to.equal(5);
  });

  it('should change the quantity by using the arrow \'Down\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity2', baseContext);

    await foClassicProductPage.setQuantityByArrowUpDown(page, 1, 'down');

    const productQuantity = await foClassicProductPage.getProductQuantity(page);
    expect(productQuantity).to.equal(1);
  });

  it('should add quantity of the product by setting input value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput', baseContext);

    await foClassicProductPage.setQuantity(page, 12);
    await foClassicProductPage.clickOnAddToCartButton(page);

    const isVisible = await foClassicModalBlockCartPage.isBlockCartModalVisible(page);
    expect(isVisible).to.equal(true);
  });

  it('should click on continue shopping and check that the modal is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

    const isNotVisible = await foClassicModalBlockCartPage.continueShopping(page);
    expect(isNotVisible).to.equal(true);
  });

  it('should check the cart notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

    const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(12);
  });

  it('should set \'-24\' in the quantity input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput2', baseContext);

    await foClassicProductPage.setQuantity(page, '-24');
    await foClassicProductPage.clickOnAddToCartButton(page);

    const isVisible = await foClassicModalBlockCartPage.isBlockCartModalVisible(page);
    expect(isVisible).to.equal(true);
  });

  it('should click on continue shopping and check that the modal is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

    const isNotVisible = await foClassicModalBlockCartPage.continueShopping(page);
    expect(isNotVisible).to.equal(true);
  });

  it('should check the cart notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

    const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(13);
  });

  it('should set \'Prestashop\' in the quantity input and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput3', baseContext);

    await foClassicProductPage.addProductToTheCart(page, 'Prestashop');

    const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(14);
  });

  it('should remove product from shopping cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

    await foClassicCartPage.deleteProduct(page, 1);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(0);
  });
});
