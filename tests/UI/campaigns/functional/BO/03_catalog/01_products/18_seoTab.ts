// Import utils
import testContext from '@utils/testContext';

// Import BO pages
import createProductPage from '@pages/BO/catalog/products/add';
import seoTab from '@pages/BO/catalog/products/add/seoTab';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  dataProducts,
  FakerProduct,
  foClassicCategoryPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_seoTab';

describe('BO - Catalog - Products : Seo tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
    name: 'Oriental fresh chair',
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });
  // Data to edit standard product
  const editProductData: FakerProduct = new FakerProduct({
    metaTitle: 'lorem ipsum',
    metaDescription: 'lorem ipsum',
    friendlyUrl: 'lorem ipsum',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in SEO tab
  describe('Check all options in SEO tab', async () => {
    it('should fill all fields on SEO Tab and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await createProductPage.goToTab(page, 'seo');
      await seoTab.setMetaTitle(page, editProductData.metaTitle!);
      await seoTab.setMetaDescription(page, editProductData.metaDescription!);
      await seoTab.setFriendlyUrl(page, editProductData.friendlyUrl!);
      await createProductPage.clickOnSaveProductButton(page);

      const errorMessage = await seoTab.getErrorMessageOfFriendlyUrl(page);
      expect(errorMessage).to.eq('"lorem ipsum" is invalid - Language: English (English)');
    });

    it('should edit friendly URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFriendlyURL', baseContext);

      await seoTab.setFriendlyUrl(page, 'lorem-ipsum');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should reset URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetURL', baseContext);

      await seoTab.clickOnGenerateUrlFromNameButton(page);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);

      const friendlyUrl = await seoTab.getValue(page, 'link_rewrite', '1');
      expect(friendlyUrl).to.eq('oriental-fresh-chair');
    });

    it('should switch the product status to off', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'switchProductStatusToOff', baseContext);

      await createProductPage.setProductStatus(page, false);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it(`'should choose redirectionPage 'Products : ${dataProducts.demo_1.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseRedirectionPage2', baseContext);

      await seoTab.selectRedirectionPage(page, 'Permanent redirection to a product (301)');
      await seoTab.searchOptionTarget(page, dataProducts.demo_1.name);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it(`should preview product and check '${dataProducts.demo_1.name}' page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should choose redirectionPage \'Category : Home accessories\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseRedirectionPage', baseContext);

      await seoTab.selectRedirectionPage(page, 'Permanent redirection to a category (301)');
      await seoTab.searchOptionTarget(page, 'Home accessories');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product and check \'Home Accessories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      const currentUrl = await foClassicProductPage.getCurrentURL(page);
      const newUrl = currentUrl.split('token');
      await foClassicProductPage.goTo(page, newUrl[0]);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains('Home Accessories');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should add tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addTag', baseContext);

      await seoTab.setTag(page, 'welcome');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should switch the product status to on', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'switchProductStatusToOn', baseContext);

      await createProductPage.setProductStatus(page, true);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should search the new tag \'welcome\' from the search bar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchTag', baseContext);

      await foClassicProductPage.searchProduct(page, 'welcome');

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

      const numberOfProducts = await foClassicCategoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.eql(1);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await createProductPage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
