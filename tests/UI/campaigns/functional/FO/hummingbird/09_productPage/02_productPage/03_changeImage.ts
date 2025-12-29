import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  FakerProduct,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
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

  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 2,
    coverImage: 'coverImage.jpg',
    thumbImage: 'thumbImage.jpg',
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    await utilsFile.generateImage(newProductData.coverImage!);
    await utilsFile.generateImage(newProductData.thumbImage!);
    await utilsFile.generateImage('secondThumbImage.jpg');
    await utilsFile.generateImage('thirdThumbImage.jpg');
    await utilsFile.generateImage('fourthThumbImage.jpg');
    await utilsFile.generateImage('fifthThumbImage.jpg');
    await utilsFile.generateImage('sixthThumbImage.jpg');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(newProductData.coverImage!);
    await utilsFile.deleteFile(newProductData.thumbImage!);
    await utilsFile.deleteFile('secondThumbImage.jpg');
    await utilsFile.deleteFile('thirdThumbImage.jpg');
    await utilsFile.deleteFile('fourthThumbImage.jpg');
    await utilsFile.deleteFile('fifthThumbImage.jpg');
    await utilsFile.deleteFile('sixthThumbImage.jpg');
  });

  describe(`PRE-TEST: Create new product '${newProductData.name}' with 7 images`, async () => {
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

    it('should add 7 images', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await boProductsCreateTabDescriptionPage.addProductImages(page,
        [newProductData.coverImage, newProductData.thumbImage, 'secondThumbImage.jpg', 'thirdThumbImage.jpg',
          'fourthThumbImage.jpg', 'fifthThumbImage.jpg', 'sixthThumbImage.jpg']);

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.equal(7);
    });
  });

  describe('FO: Change image', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, newProductData.name);

      const productsNumber = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(productsNumber).to.equal(1);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(newProductData.name);
    });

    it('should display the third image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayThirdImage', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.equals('0');

      const coverPositionAfterSelect = await foHummingbirdProductPage.selectThumbImage(page, 3);
      expect(coverPosition).to.not.equal(coverPositionAfterSelect);
      expect(coverPositionAfterSelect).to.equals('2');
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.equals('2');

      const coverPositionAfterSelect = await foHummingbirdProductPage.selectThumbImage(page, 1);
      expect(coverPosition).to.not.equal(coverPositionAfterSelect);
      expect(coverPositionAfterSelect).to.equals('0');
    });

    it('should click on the arrow right and check the cover image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickRight', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.equals('0');

      await foHummingbirdProductPage.scrollBoxArrowsImages(page, 'next');

      const coverPositionAfterNext = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.not.equal(coverPositionAfterNext);
      expect(coverPositionAfterNext).to.equals('1');
    });

    it('should zoom the cover image and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'zoomImage', baseContext);

      const isModalVisible = await foHummingbirdProductPage.zoomCoverImage(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click on the arrow right in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSecondLittleImage', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImageFromProductModal(page);
      expect(coverPosition).to.equals('1');

      const coverPositionAfterNext = await foHummingbirdProductPage.clickOnArrowNextPrevInProductModal(page, 'next');
      expect(coverPosition).to.not.equal(coverPositionAfterNext);
      expect(coverPositionAfterNext).to.equals('2');
    });

    it('should close the product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await foHummingbirdProductPage.closeProductModal(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should click on the arrow left and check the cover image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickLeft', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.equals('2');

      await foHummingbirdProductPage.scrollBoxArrowsImages(page, 'prev');

      const coverPositionAfterPrev = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.not.equal(coverPositionAfterPrev);
      expect(coverPositionAfterPrev).to.equals('1');
    });

    it('should click on the last image and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickLastImage', baseContext);

      const coverPosition = await foHummingbirdProductPage.getCoverImage(page);
      expect(coverPosition).to.equals('1');

      const coverPositionAfterSelect = await foHummingbirdProductPage.selectThumbImage(page, 7);
      expect(coverPosition).to.not.equal(coverPositionAfterSelect);
      expect(coverPositionAfterSelect).to.equals('6');
    });
  });

  // Post-condition : Delete created product
  deleteProductTest(newProductData, `${baseContext}_postTest`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest2`);
});
