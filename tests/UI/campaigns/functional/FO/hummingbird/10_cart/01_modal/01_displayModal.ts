import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_modal_displayModal';

describe('FO - Cart : Display modal when adding a product to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display modal when adding a product to cart', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should add the first product to cart by quick view and click on continue button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.setQuantityAndAddToCart(page, 2);

      const isBlockCartModal = await foHummingbirdModalBlockCartPage.isBlockCartModalVisible(page);
      expect(isBlockCartModal).to.equal(true);

      const successMessage = await foHummingbirdModalBlockCartPage.getBlockCartModalTitle(page);
      expect(successMessage).to.contains(foHummingbirdHomePage.successAddToCartMessage);
    });

    it('should click on continue shopping button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

      const isModalNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should go to the second product page and add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 2);
      // Add the product to the cart
      await foHummingbirdProductPage.addProductToTheCart(page, 3);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const notificationsNumber = await foHummingbirdHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
