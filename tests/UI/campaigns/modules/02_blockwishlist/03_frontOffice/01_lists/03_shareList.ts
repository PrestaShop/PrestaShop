// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  dataModules,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyAccountPage,
  foClassicMyWishlistsPage,
  foClassicMyWishlistsViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import {resetModule} from '@commonTests/BO/modules/moduleManager';

const baseContext: string = 'modules_blockwishlist_frontOffice_lists_shareList';

describe('Wishlist module - Share a list', async () => {
  const wishlistName: string = 'Ma liste de souhaits';

  let browserContext: BrowserContext;
  let page: Page;
  let wishlistUrl: string;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Share a list', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogin', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount1', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My Wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists1', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);
    });

    it('should click on the share icon and cancel the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickShareAndCancel', baseContext);

      await foClassicMyWishlistsPage.clickShareWishlistButton(page, 1);

      const hasModalShare = await foClassicModalWishlistPage.hasModalShare(page);
      expect(hasModalShare).to.equal(true);

      const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalShare(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the share icon and copy the text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickShareAndCopyText', baseContext);

      await foClassicMyWishlistsPage.clickShareWishlistButton(page, 1);

      const hasModalLogin = await foClassicModalWishlistPage.hasModalShare(page);
      expect(hasModalLogin).to.equal(true);

      const textToast = await foClassicModalWishlistPage.clickShareOnModalShare(page);
      expect(textToast).to.equal(foClassicModalWishlistPage.messageLinkSharedWishlist);
    });

    it('should click on the Create new list link and cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewListAndCancel', baseContext);

      await foClassicMyWishlistsPage.clickCreateWishlistButton(page);

      const hasModalCreate = await foClassicModalWishlistPage.hasModalCreate(page);
      expect(hasModalCreate).to.equal(true);

      const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalCreate(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the Create new list link and create it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewListAndCreate', baseContext);

      await foClassicMyWishlistsPage.clickCreateWishlistButton(page);

      const hasModalCreate = await foClassicModalWishlistPage.hasModalCreate(page);
      expect(hasModalCreate).to.equal(true);

      await foClassicModalWishlistPage.setNameOnModalCreate(page, wishlistName);

      const textToast = await foClassicModalWishlistPage.clickCreateOnModalCreate(page);
      expect(textToast).to.equal(foClassicModalWishlistPage.messageWishlistCreated);
    });

    it('should click on the share icon (in dropdown) and cancel the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDropdownShareAndCancel', baseContext);

      await foClassicMyWishlistsPage.clickShareWishlistButton(page, 2);

      const hasModalShare = await foClassicModalWishlistPage.hasModalShare(page);
      expect(hasModalShare).to.equal(true);

      const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalShare(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the share icon (in dropdown) and copy the text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDropdownShareAndCopyText', baseContext);

      await foClassicMyWishlistsPage.clickShareWishlistButton(page, 2);

      const hasModalLogin = await foClassicModalWishlistPage.hasModalShare(page);
      expect(hasModalLogin).to.equal(true);

      const textToast = await foClassicModalWishlistPage.clickShareOnModalShare(page);
      expect(textToast).to.equal(foClassicModalWishlistPage.messageLinkSharedWishlist);

      wishlistUrl = await foClassicMyWishlistsPage.getClipboardText(page);
      expect(wishlistUrl).to.be.a('string');
      expect(wishlistUrl.length).to.be.gt(0);
    });

    it('should go to the shared wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSharedWishlistLogged', baseContext);

      await foClassicMyWishlistsPage.goTo(page, wishlistUrl);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logout', baseContext);

      await foClassicMyWishlistsViewPage.logout(page);
      await foClassicMyWishlistsViewPage.clickOnHeaderLink(page, 'Logo');

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);
    });

    it('should return to the shared wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSharedWishlistUnlogged', baseContext);

      await foClassicLoginPage.goTo(page, wishlistUrl);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });
  });

  resetModule(dataModules.blockwishlist, `${baseContext}_postTest_0`);
});
