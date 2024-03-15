// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_cart_modal_displayModal';

describe('FO - cart : Display modal when adding a product to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display modal when adding a product to cart', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should add the first product to cart by quick view and click on continue button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.setQuantityAndAddToCart(page, 2);

      const isBlockCartModal = await blockCartModal.isBlockCartModalVisible(page);
      expect(isBlockCartModal).to.equal(true);

      const successMessage = await blockCartModal.getBlockCartModalTitle(page);
      expect(successMessage).to.contains(homePage.successAddToCartMessage);
    });

    it('should click on continue shopping button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

      const isModalNotVisible = await blockCartModal.continueShopping(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should go to the second product page and add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductPage', baseContext);

      await homePage.goToProductPage(page, 2);
      // Add the product to the cart
      await productPage.addProductToTheCart(page, 3);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
