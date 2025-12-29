import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabOptionsPage,
  type BrowserContext,
  FakerProduct,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_displayTag';

describe('FO - Product page - Product page : Display tag products (New, On sale, Pack...)', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;
  // Data to create pack of products
  const newProductData: FakerProduct = new FakerProduct({
    type: 'pack',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    pack: [
      {
        reference: 'demo_11',
        quantity: 10,
      },
    ],
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: true,
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

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

  describe('Create pack of products', async () => {
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

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePackOfProducts', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.packOfProductsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should choose the option \'Web only\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWebOnly', baseContext);

      await boProductsCreatePage.goToTab(page, 'options');
      await boProductsCreateTabOptionsPage.setWebOnly(page, true);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Check tags on FO Home page and Product page', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.viewMyShop(page);
      await foHummingbirdProductPage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductsPage', baseContext);

      await foHummingbirdCategoryPage.goToNextPage(page);

      productsNumber = await foHummingbirdCategoryPage.getProductsNumber(page);
      expect(productsNumber).to.not.equal(19);
    });

    it('should check the tag \'New, pack, out-of-stock and Online only\' for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTagsInAllProductPage', baseContext);

      const flagText = await foHummingbirdCategoryPage.getProductTag(page, productsNumber - 12);
      expect(flagText).to.contains('Online only')
        .and.to.contain('New')
        .and.to.contain('Pack')
        .and.to.contain('Out-of-Stock');
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foHummingbirdCategoryPage.goToProductPage(page, productsNumber - 12);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New, pack, out-of-stock and Online only\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnSaleFlag', baseContext);

      const flagText = await foHummingbirdProductPage.getProductTag(page);
      expect(flagText).to.contains('Online only')
        .and.to.contain('New')
        .and.to.contain('Pack')
        .and.to.contain('Out-of-Stock');
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_1`);

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest_2`);
});
