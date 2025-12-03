import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataProducts,
  foClassicCartPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_cart_cart_displayModalProductCustomization';

describe('FO - Cart : Display modal of product customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customText: string = 'Hello world!';

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

  it(`should search for the product '${dataProducts.demo_14.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_14.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_14.name);
  });

  it('should add custom text and add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicProductPage.setProductCustomizations(page, [customText]);

    await foClassicProductPage.clickOnAddToCartButton(page);

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

  it('should go to the cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCartPage', baseContext);

    await foClassicProductPage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should click on product customization and check the modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickCustomization', baseContext);

    const isModalVisible = await foClassicCartPage.clickOnProductCustomization(page, 1);
    expect(isModalVisible).to.equal(true);
  });

  it('should check the customization modal content', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getModalContent', baseContext);

    const modalContent = await foClassicCartPage.getProductCustomizationModal(page);
    expect(modalContent).to.equal(`Type your text here ${customText}`);
  });

  it('should close the modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

    const isModalNotVisible = await foClassicCartPage.closeProductCustomizationModal(page, 1);
    expect(isModalNotVisible).to.equal(true);
  });
});
