// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {deleteProductV2Test} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import descriptionTab from '@pages/BO/catalog/productsV2/add/descriptionTab';

// Import data
import ProductFaker from '@data/faker/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'productV2_functional_descriptionTab';

describe('BO - Catalog - Products : Description tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  const productCoverImage: string = 'productCoverImage.png';
  const replaceProductCoverImage: string = 'productReplaceCoverImage.png';

  // Data to create product
  const productData: ProductFaker = new ProductFaker({
    type: 'standard',
    name: 'hello word',
    coverImage: 'cover.jpg',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage(productCoverImage);
    await files.generateImage(replaceProductCoverImage);
    if (productData.coverImage) {
      await files.generateImage(productData.coverImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(productCoverImage);
    await files.deleteFile(replaceProductCoverImage);
    if (productData.coverImage) {
      await files.deleteFile(productData.coverImage);
    }
  });

  describe('Create product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page and set product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await productsPage.clickOnAddNewProduct(page);
      await createProductsPage.setProductName(page, productData.name);

      await createProductsPage.setProductStatus(page, productData.status);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add 3 images', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await descriptionTab.addProductImages(page, [productData.coverImage, productCoverImage, replaceProductCoverImage]);

      const numOfImages = await descriptionTab.getNumberOfImages(page);
      expect(numOfImages).to.eq(3);
    });

    it('should set image information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

      const message = await descriptionTab.setProductImageInformation(page, 2, true, 'Caption EN', 'Caption FR');
      expect(message).to.be.eq(descriptionTab.settingUpdatedMessage);
    });

    it('should click on the magnifying glass', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'zoomImage', baseContext);

      const isImageZoomed = await descriptionTab.clickOnMagnifyingGlass(page);
      expect(isImageZoomed).to.eq(true);
    });

    it('should close the image zoom', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeZoom', baseContext);

      const isZoomClosed = await descriptionTab.closeImageZoom(page);
      expect(isZoomClosed).to.eq(true);
    });

    it('should replace image selection', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'replaceImageSelection', baseContext);

      const message = await descriptionTab.replaceImageSelection(page, replaceProductCoverImage);
      expect(message).to.be.eq(descriptionTab.settingUpdatedMessage);
    });

    it('should delete the image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImage', baseContext);

      const message = await descriptionTab.deleteImage(page);
      expect(message).to.be.eq(descriptionTab.successfulMultiDeleteMessage);
    });

    it('should select the first image and click on select all products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectProduct', baseContext);

      await descriptionTab.setProductImageInformation(page, 1, undefined, undefined, undefined, true, false);
    });

    it('should set product description and summary', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductDescription', baseContext);

      await descriptionTab.setProductDescription(page, productData);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCategory', baseContext);

      await descriptionTab.addNewCategory(page, ['Clothes', 'Men']);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);

      const selectedCategories = await descriptionTab.getSelectedCategories(page);
      expect(selectedCategories).to.eq('Home x Clothes x Men x');
    });

    it('should check that we can delete the 2 categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeleteIcon', baseContext);

      let isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 0);
      expect(isDeleteIconVisible).to.eq(false);

      isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 1);
      expect(isDeleteIconVisible).to.eq(true);

      isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 2);
      expect(isDeleteIconVisible).to.eq(true);
    });

    it('should choose default category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCategory', baseContext);

      await descriptionTab.chooseDefaultCategory(page, 2);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check that we can delete the first and the last category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeleteIcon2', baseContext);

      let isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 0);
      expect(isDeleteIconVisible).to.eq(true);

      isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 1);
      expect(isDeleteIconVisible).to.eq(false);

      isDeleteIconVisible = await descriptionTab.isDeleteCategoryIconVisible(page, 2);
      expect(isDeleteIconVisible).to.eq(true);
    });

    it('should choose brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseBrand', baseContext);

      await descriptionTab.chooseBrand(page, 2);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add related product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addRelatedProduct', baseContext);

      await descriptionTab.addRelatedProduct(page, 't-shirt');

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  deleteProductV2Test(productData, `${baseContext}_postTest_0`);
});
