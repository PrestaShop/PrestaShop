// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_productPage_quickView_changeImage';

/*
Pre-condition:
- Install hummingbird theme
- Create product with 2 images
Scenario:
- Go to FO
- Quick view the created product
- Change second image
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page - Quick view : Change image', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product out of stock not allowed
  const productWith2Images: ProductData = new ProductData({
    type: 'standard',
    quantity: 2,
    coverImage: 'coverImage.jpg',
    thumbImage: 'thumbImage.jpg',
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_0`);

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productWith2Images, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage(productWith2Images.coverImage!);
    await files.generateImage(productWith2Images.thumbImage!);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(productWith2Images.coverImage!);
    await files.deleteFile(productWith2Images.thumbImage!);
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

      const firstCoverImageURL = await quickViewModal.getQuickViewImageMain(page);
      await quickViewModal.selectThumbImage(page, 2);
      const secondCoverImageURL = await quickViewModal.getQuickViewImageMain(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const firstCoverImageURL = await quickViewModal.getQuickViewImageMain(page);
      await quickViewModal.selectThumbImage(page, 1);
      const secondCoverImageURL = await quickViewModal.getQuickViewImageMain(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_0`);

  // Post-condition : Delete created product
  deleteProductTest(productWith2Images, `${baseContext}_postTest_1`);
});
