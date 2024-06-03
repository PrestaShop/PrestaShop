// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerProduct,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_changeImage';

/*
Pre-condition:
- Install the theme hummingbird
- Create product with 7 images
Scenario:
- Go to FO
- Go to the created product page
- Change image
- Scroll from images list ans select image
- Zoom the cover image and change image
Post-condition:
- Delete created product
- Uninstall the theme hummingbird
 */
describe('FO - Product page - Quick view : Change image', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 2,
    coverImage: 'coverImage.jpg',
    thumbImage: 'thumbImage.jpg',
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage(newProductData.coverImage!);
    await files.generateImage(newProductData.thumbImage!);
    await files.generateImage('secondThumbImage.jpg');
    await files.generateImage('thirdThumbImage.jpg');
    await files.generateImage('fourthThumbImage.jpg');
    await files.generateImage('fifthThumbImage.jpg');
    await files.generateImage('sixthThumbImage.jpg');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(newProductData.coverImage!);
    await files.deleteFile(newProductData.thumbImage!);
    await files.deleteFile('secondThumbImage.jpg');
    await files.deleteFile('thirdThumbImage.jpg');
    await files.deleteFile('fourthThumbImage.jpg');
    await files.deleteFile('fifthThumbImage.jpg');
    await files.deleteFile('sixthThumbImage.jpg');
  });

  describe(`PRE-TEST: Create new product '${newProductData.name}' with 7 images`, async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page and set product name and status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await productsPage.clickOnAddNewProduct(page);
      await createProductsPage.setProductName(page, newProductData.name);

      await createProductsPage.setProductStatus(page, newProductData.status);

      const createProductMessage = await createProductsPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add 7 images', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await descriptionTab.addProductImages(page,
        [newProductData.coverImage, newProductData.thumbImage, 'secondThumbImage.jpg', 'thirdThumbImage.jpg',
          'fourthThumbImage.jpg', 'fifthThumbImage.jpg', 'sixthThumbImage.jpg']);

      const numOfImages = await descriptionTab.getNumberOfImages(page);
      expect(numOfImages).to.equal(7);
    });
  });

  describe('FO: Change image', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await homePage.searchProduct(page, newProductData.name);

      const productsNumber = await searchResultsPage.getSearchResultsNumber(page);
      expect(productsNumber).to.equal(1);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.equal(newProductData.name);
    });

    it('should display the third image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayThirdImage', baseContext);

      const coverImageURL = await productPage.getCoverImage(page);

      const thirdCoverImageURL = await productPage.selectThumbImage(page, 3);
      expect(coverImageURL).to.not.equal(thirdCoverImageURL);
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const coverImagePosition = await productPage.getCoverImage(page);

      const secondCoverImagePosition = await productPage.selectThumbImage(page, 1);
      expect(coverImagePosition).to.not.equal(secondCoverImagePosition);
    });

    it('should click on the arrow right and check the cover image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickRight', baseContext);

      const coverImagePosition = await productPage.getCoverImage(page);
      await productPage.scrollBoxArrowsImages(page, 'next');

      const secondCoverImagePosition = await productPage.getCoverImage(page);
      expect(coverImagePosition).to.not.equal(secondCoverImagePosition);
    });

    it('should zoom the cover image and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'zoomImage', baseContext);

      const isModalVisible = await productPage.zoomCoverImage(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click on the arrow right in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSecondLittleImage', baseContext);

      const coverImagePosition = await productPage.getCoverImageFromProductModal(page);

      const thirdCoverImagePosition = await productPage.clickOnArrowNextPrevInProductModal(page, 'next');
      expect(coverImagePosition).to.not.equal(thirdCoverImagePosition);
    });

    it('should close the product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await productPage.closeProductModal(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should click on the arrow left and check the cover image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickLeft', baseContext);

      const coverImagePosition = await productPage.getCoverImage(page);
      await productPage.scrollBoxArrowsImages(page, 'prev');

      const secondCoverImagePosition = await productPage.getCoverImage(page);
      expect(coverImagePosition).to.not.equal(secondCoverImagePosition);
    });

    it('should click on the last image and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickLastImage', baseContext);

      const coverImagePosition = await productPage.getCoverImage(page);

      const lastCoverImagePosition = await productPage.selectThumbImage(page, 7);
      expect(coverImagePosition).to.not.equal(lastCoverImagePosition);
    });
  });

  // Post-condition : Delete created product
  deleteProductTest(newProductData, `${baseContext}_postTest`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest2`);
});
