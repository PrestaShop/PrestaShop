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

const baseContext: string = 'functional_FO_classic_cart_modal_displayModal';

describe('FO - Cart : Display modal when adding a product to cart', async () => {
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

  it('should add the first product to cart by quick view and click on continue button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);
    await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);

    const isBlockCartModal = await foClassicModalBlockCartPage.isBlockCartModalVisible(page);
    expect(isBlockCartModal).to.equal(true);

    const successMessage = await foClassicModalBlockCartPage.getBlockCartModalTitle(page);
    expect(successMessage).to.contains(foClassicHomePage.successAddToCartMessage);

    const isModalNotVisible = await foClassicModalBlockCartPage.continueShopping(page);
    expect(isModalNotVisible).to.eq(true);
  });

  it('should go to the second product page and add the product to the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 2);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page, 3);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
  });

  it('should check notifications number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

    const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.eq(5);
  });
});
