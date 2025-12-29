import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_displayModalProductCustomization';

describe('FO - Cart : Display modal of product customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customText: string = 'Hello world!';

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display modal of product customization', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_14.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_14.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_14.name);
    });

    it('should add custom text and add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.setProductCustomizations(page, [customText]);

      await foHummingbirdProductPage.clickOnAddToCartButton(page);

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

    it('should go to the cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCartPage', baseContext);

      await foHummingbirdProductPage.goToCartPage(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should click on product customization and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickCustomization', baseContext);

      const isModalVisible = await foHummingbirdCartPage.clickOnProductCustomization(page, 1);
      expect(isModalVisible).to.equal(true);
    });

    it('should check the customization modal content', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getModalContent', baseContext);

      const modalContent = await foHummingbirdCartPage.getProductCustomizationModal(page);
      expect(modalContent).to.equal(`Type your text here ${customText}`);
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await foHummingbirdCartPage.closeProductCustomizationModal(page, 1);
      expect(isModalNotVisible).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
