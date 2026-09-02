import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_descriptionTab';

describe('BO - Catalog - Products : Description tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  const productCoverImage: string = 'productCoverImage.png';
  const replaceProductCoverImage: string = 'productReplaceCoverImage.png';

  // Data to create product
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    name: 'hello word',
    coverImage: 'cover.jpg',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    await utilsFile.generateImage(productCoverImage);
    await utilsFile.generateImage(replaceProductCoverImage);
    if (productData.coverImage) {
      await utilsFile.generateImage(productData.coverImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(productCoverImage);
    await utilsFile.deleteFile(replaceProductCoverImage);
    if (productData.coverImage) {
      await utilsFile.deleteFile(productData.coverImage);
    }
  });

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

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page and set product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);
      await boProductsCreatePage.setProductName(page, productData.name);

      await boProductsCreatePage.setProductStatus(page, productData.status);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add 3 images', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await boProductsCreateTabDescriptionPage.addProductImages(
        page,
        [productData.coverImage, productCoverImage, replaceProductCoverImage],
      );

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.eq(3);
    });

    it('should set image information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

      const message = await boProductsCreateTabDescriptionPage.setProductImageInformation(
        page,
        2,
        true,
        'Caption EN',
        'Caption FR',
      );
      expect(message).to.be.eq(boProductsCreateTabDescriptionPage.settingUpdatedMessage);
    });

    it('should click on the magnifying glass', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'zoomImage', baseContext);

      const isImageZoomed = await boProductsCreateTabDescriptionPage.clickOnMagnifyingGlass(page);
      expect(isImageZoomed).to.eq(true);
    });

    it('should close the image zoom', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeZoom', baseContext);

      const isZoomClosed = await boProductsCreateTabDescriptionPage.closeImageZoom(page);
      expect(isZoomClosed).to.eq(true);
    });

    it('should replace image selection', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'replaceImageSelection', baseContext);

      const message = await boProductsCreateTabDescriptionPage.replaceImageSelection(page, replaceProductCoverImage);
      expect(message).to.be.eq(boProductsCreateTabDescriptionPage.settingUpdatedMessage);
    });

    it('should delete the image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImage', baseContext);

      const message = await boProductsCreateTabDescriptionPage.deleteImage(page);
      expect(message).to.be.eq(boProductsCreateTabDescriptionPage.successfulMultiDeleteMessage);
    });

    it('should select the first image and click on select all products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectProduct', baseContext);

      await boProductsCreateTabDescriptionPage.setProductImageInformation(page, 1, undefined, undefined, undefined, true, false);
    });

    it('should set product description and summary', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductDescription', baseContext);

      await boProductsCreateTabDescriptionPage.setProductDescription(page, productData);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCategory', baseContext);

      await boProductsCreateTabDescriptionPage.addNewCategory(page, ['Clothes', 'Men']);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const selectedCategories = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);
      expect(selectedCategories).to.eq('Home x Clothes x Men x');
    });

    it('should check that we can delete the 2 categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeleteIcon', baseContext);

      let isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 0);
      expect(isDeleteIconVisible).to.eq(false);

      isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 1);
      expect(isDeleteIconVisible).to.eq(true);

      isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 2);
      expect(isDeleteIconVisible).to.eq(true);
    });

    it('should choose default category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCategory', baseContext);

      await boProductsCreateTabDescriptionPage.chooseDefaultCategory(page, 'Clothes');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check that we can delete the first and the last category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeleteIcon2', baseContext);

      let isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 0);
      expect(isDeleteIconVisible).to.eq(true);

      isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 1);
      expect(isDeleteIconVisible).to.eq(false);

      isDeleteIconVisible = await boProductsCreateTabDescriptionPage.isDeleteCategoryIconVisible(page, 2);
      expect(isDeleteIconVisible).to.eq(true);
    });

    it('should choose brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseBrand', baseContext);

      await boProductsCreateTabDescriptionPage.chooseBrand(page, 'Studio Design');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add related product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addRelatedProduct', baseContext);

      await boProductsCreateTabDescriptionPage.addRelatedProduct(page, 't-shirt');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  deleteProductTest(productData, `${baseContext}_postTest_0`);
});
