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
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_changeImage';

/*
Pre-condition:
- Create product with 4 images
Scenario:
- Go to FO
- Go to the created product page
- Change image
- Scroll from images list ans select image
- Zoom the cover image and change image
Post-condition:
- Delete created product
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

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    await utilsFile.generateImage(newProductData.coverImage!);
    await utilsFile.generateImage(newProductData.thumbImage!);
    await utilsFile.generateImage('secondThumbImage.jpg');
    await utilsFile.generateImage('thirdThumbImage.jpg');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(newProductData.coverImage!);
    await utilsFile.deleteFile(newProductData.thumbImage!);
    await utilsFile.deleteFile('secondThumbImage.jpg');
    await utilsFile.deleteFile('thirdThumbImage.jpg');
  });

  describe(`PRE-TEST: Create new product '${newProductData.name}' with 4 images`, async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page and set product name and status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);
      await boProductsCreatePage.setProductName(page, newProductData.name);

      await boProductsCreatePage.setProductStatus(page, newProductData.status);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add 4 images', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await boProductsCreateTabDescriptionPage.addProductImages(page,
        [newProductData.coverImage, newProductData.thumbImage, 'secondThumbImage.jpg', 'thirdThumbImage.jpg']);

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.equal(4);
    });
  });

  describe('FO: Change image', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await foClassicHomePage.searchProduct(page, newProductData.name);

      const productsNumber = await foClassicSearchResultsPage.getSearchResultsNumber(page);
      expect(productsNumber).to.equal(1);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(newProductData.name);
    });

    it('should display the second image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displaySecondImage', baseContext);

      const firstCoverImageURL = await foClassicProductPage.getCoverImage(page);

      const secondCoverImageURL = await foClassicProductPage.selectThumbImage(page, 2);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const firstCoverImageURL = await foClassicProductPage.getCoverImage(page);

      const secondCoverImageURL = await foClassicProductPage.selectThumbImage(page, 1);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });

    it('should click on the arrow right and click on the 4th product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'display4ThImage', baseContext);

      const coverImageURL = await foClassicProductPage.getCoverImage(page);
      await foClassicProductPage.scrollBoxArrowsImages(page, 'right');

      const fourthCoverImageURL = await foClassicProductPage.selectThumbImage(page, 4);
      expect(coverImageURL).to.not.equal(fourthCoverImageURL);
    });

    it('should zoom the cover image and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'zoomImage', baseContext);

      const isModalVisible = await foClassicProductPage.zoomCoverImage(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click on the third little image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSecondLittleImage', baseContext);

      const coverImageURL = await foClassicProductPage.getCoverImageFromProductModal(page);

      const thirdCoverImageURL = await foClassicProductPage.selectThumbImageFromProductModal(page, 3);
      expect(coverImageURL).to.not.equal(thirdCoverImageURL);
    });

    it('should close the product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await foClassicProductPage.closeProductModal(page);
      expect(isModalNotVisible).to.equal(true);
    });
  });

  // Post-condition : Delete created product
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
