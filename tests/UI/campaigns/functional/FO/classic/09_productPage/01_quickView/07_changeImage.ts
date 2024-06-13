// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  FakerProduct,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_quickView_changeImage';

/*
Pre-condition:
- Create product with 2 images
Scenario:
- Go to FO
- Quick view the created product
- Change second image
Post-condition:
- Delete created product
 */
describe('FO - Product page - Quick view : Change image', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product out of stock not allowed
  const productWith2Images: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 2,
    coverImage: 'coverImage.jpg',
    thumbImage: 'thumbImage.jpg',
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productWith2Images, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    await utilsFile.generateImage(productWith2Images.coverImage!);
    await utilsFile.generateImage(productWith2Images.thumbImage!);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(productWith2Images.coverImage!);
    await utilsFile.deleteFile(productWith2Images.thumbImage!);
  });

  describe('Change image', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await homePage.searchProduct(page, productWith2Images.name);

      const productsNumber = await searchResultsPage.getSearchResultsNumber(page);
      expect(productsNumber).to.equal(1);
    });

    it('should quick view the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should display the second image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displaySecondImage', baseContext);

      const firstCoverImageURL = await quickViewModal.getQuickViewCoverImage(page);
      await quickViewModal.selectThumbImage(page, 2);
      const secondCoverImageURL = await quickViewModal.getQuickViewCoverImage(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const firstCoverImageURL = await quickViewModal.getQuickViewCoverImage(page);
      await quickViewModal.selectThumbImage(page, 1);
      const secondCoverImageURL = await quickViewModal.getQuickViewCoverImage(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });
  });

  // Post-condition : Delete created product
  deleteProductTest(productWith2Images, `${baseContext}_postTest`);
});
