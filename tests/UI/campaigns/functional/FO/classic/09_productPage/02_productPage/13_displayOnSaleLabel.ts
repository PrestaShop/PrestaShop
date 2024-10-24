// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import createProductPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  FakerProduct,
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayOnSaleLabel';

describe('FO - Product page - Product page : Display on sale label', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;
  // Data to create pack of products
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    onSale: true,
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create new product', async () => {
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
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await createProductPage.closeSfToolBar(page);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  describe('Check tags on FO Home page and Product page', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on preview button
      page = await createProductPage.viewMyShop(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductsPage', baseContext);

      await foClassicCategoryPage.goToNextPage(page);

      productsNumber = await foClassicCategoryPage.getProductsNumber(page);
      expect(productsNumber).to.not.equal(19);
    });

    it('should check the tag \'New, pack, out-of-stock and Online only\' for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTagsInAllProductPage', baseContext);

      const flagText = await foClassicCategoryPage.getProductTag(page, productsNumber - 12);
      expect(flagText).to.contains('On sale!')
        .and.to.contain('New')
        .and.to.contain('Out-of-Stock');
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foClassicCategoryPage.goToProductPage(page, productsNumber - 12);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New, pack, out-of-stock and Online only\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnSaleFlag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('On sale!')
        .and.to.contain('New')
        .and.to.contain('Out-of-Stock');
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
