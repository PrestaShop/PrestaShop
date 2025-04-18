import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_quickView_changeQuantity';

/*
Scenario:
- Go to FO
- Quick view third product
- Click up/down on quantity input
- Set quantity input (good/bad value)
 */
describe('FO - Product page - Quick view : Change quantity', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

    await foClassicHomePage.quickViewProduct(page, 3);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should change the quantity by using the arrow \'UP\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity', baseContext);

    await foClassicModalQuickViewPage.setQuantityByArrowUpDown(page, 5, 'up');

    const productQuantity = await foClassicModalQuickViewPage.getProductQuantityFromQuickViewModal(page);
    expect(productQuantity).to.equal(5);
  });

  it('should change the quantity by using the arrow \'Down\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity2', baseContext);

    await foClassicModalQuickViewPage.setQuantityByArrowUpDown(page, 1, 'down');

    const productQuantity = await foClassicModalQuickViewPage.getProductQuantityFromQuickViewModal(page);
    expect(productQuantity).to.equal(1);
  });

  it('should add quantity of the product by setting input value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput', baseContext);

    await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 12);

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

    const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.equal(12);
  });

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView2', baseContext);

    await foClassicHomePage.quickViewProduct(page, 3);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should set \'-24\' in the quantity input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput2', baseContext);

    await foClassicModalQuickViewPage.setQuantity(page, '-24');
    await foClassicModalQuickViewPage.addToCartByQuickView(page);

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

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView3', baseContext);

    await foClassicHomePage.quickViewProduct(page, 3);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should set \'Prestashop\' in the quantity input and check that add to cart button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput3', baseContext);

    await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 'Prestashop');

    const isEnabled = await foClassicModalQuickViewPage.isAddToCartButtonEnabled(page);
    expect(isEnabled, 'Add to cart button is not disabled').to.equal(false);
  });
});
