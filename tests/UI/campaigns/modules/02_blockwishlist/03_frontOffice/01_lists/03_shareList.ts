// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  dataModules,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalWishlistPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyWishlistsPage,
  foHummingbirdMyWishlistsViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import {disableModule, enableModule, resetModule} from '@commonTests/BO/modules/moduleManager';

const baseContext: string = 'modules_blockwishlist_frontOffice_lists_shareList';

describe('Wishlist module - Share a list', async () => {
  // PRE-TEST : Enable Blockwishlist
  enableModule(dataModules.blockwishlist, `${baseContext}_preTest_0`);

  describe('Share a list', async () => {
    const wishlistName: string = 'Ma liste de souhaits';

    let browserContext: BrowserContext;
    let page: Page;
    let wishlistUrl: string;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogin', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount1', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My Wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists1', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);
    });

    it('should click on the share icon and cancel the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickShareAndCancel', baseContext);

      await foHummingbirdMyWishlistsPage.clickShareWishlistButton(page, 1);

      const hasModalShare = await foHummingbirdModalWishlistPage.hasModalShare(page);
      expect(hasModalShare).to.equal(true);

      const isModalVisible = await foHummingbirdModalWishlistPage.clickCancelOnModalShare(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the share icon and copy the text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickShareAndCopyText', baseContext);

      await foHummingbirdMyWishlistsPage.clickShareWishlistButton(page, 1);

      const hasModalLogin = await foHummingbirdModalWishlistPage.hasModalShare(page);
      expect(hasModalLogin).to.equal(true);

      const textToast = await foHummingbirdModalWishlistPage.clickShareOnModalShare(page);
      expect(textToast).to.equal(foHummingbirdModalWishlistPage.messageLinkSharedWishlist);
    });

    it('should click on the Create new list link and cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewListAndCancel', baseContext);

      await foHummingbirdMyWishlistsPage.clickCreateWishlistButton(page);

      const hasModalCreate = await foHummingbirdModalWishlistPage.hasModalCreate(page);
      expect(hasModalCreate).to.equal(true);

      const isModalVisible = await foHummingbirdModalWishlistPage.clickCancelOnModalCreate(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the Create new list link and create it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewListAndCreate', baseContext);

      await foHummingbirdMyWishlistsPage.clickCreateWishlistButton(page);

      const hasModalCreate = await foHummingbirdModalWishlistPage.hasModalCreate(page);
      expect(hasModalCreate).to.equal(true);

      await foHummingbirdModalWishlistPage.setNameOnModalCreate(page, wishlistName);

      const textToast = await foHummingbirdModalWishlistPage.clickCreateOnModalCreate(page);
      expect(textToast).to.equal(foHummingbirdModalWishlistPage.messageWishlistCreated);
    });

    it('should click on the share icon (in dropdown) and cancel the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDropdownShareAndCancel', baseContext);

      await foHummingbirdMyWishlistsPage.clickShareWishlistButton(page, 2);

      const hasModalShare = await foHummingbirdModalWishlistPage.hasModalShare(page);
      expect(hasModalShare).to.equal(true);

      const isModalVisible = await foHummingbirdModalWishlistPage.clickCancelOnModalShare(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the share icon (in dropdown) and copy the text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDropdownShareAndCopyText', baseContext);

      await foHummingbirdMyWishlistsPage.clickShareWishlistButton(page, 2);

      const hasModalLogin = await foHummingbirdModalWishlistPage.hasModalShare(page);
      expect(hasModalLogin).to.equal(true);

      const textToast = await foHummingbirdModalWishlistPage.clickShareOnModalShare(page);
      expect(textToast).to.equal(foHummingbirdModalWishlistPage.messageLinkSharedWishlist);

      wishlistUrl = await foHummingbirdMyWishlistsPage.getClipboardText(page);
      expect(wishlistUrl).to.be.a('string');
      expect(wishlistUrl.length).to.be.gt(0);
    });

    it('should go to the shared wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSharedWishlistLogged', baseContext);

      await foHummingbirdMyWishlistsPage.goTo(page, wishlistUrl);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logout', baseContext);

      await foHummingbirdMyWishlistsViewPage.logout(page);
      await foHummingbirdMyWishlistsViewPage.clickOnHeaderLink(page, 'Logo');

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);
    });

    it('should return to the shared wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSharedWishlistUnlogged', baseContext);

      await foHummingbirdLoginPage.goTo(page, wishlistUrl);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });
  });

  resetModule(dataModules.blockwishlist, `${baseContext}_postTest_0`);

  // POST-TEST : Disable Blockwishlist
  disableModule(dataModules.blockwishlist, `${baseContext}_postTest_1`);
});
