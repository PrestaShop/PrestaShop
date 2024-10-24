// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  type BrowserContext,
  FakerProduct,
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const productWith2Images: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 2,
    coverImage: 'coverImage.jpg',
    thumbImage: 'thumbImage.jpg',
  });

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_0`);

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productWith2Images, `${baseContext}_preTest_1`);

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

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, productWith2Images.name);

      const productsNumber = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(productsNumber).to.equal(1);
    });

    it('should quick view the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should display the second image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displaySecondImage', baseContext);

      const firstCoverImageURL = await foHummingbirdModalQuickViewPage.getQuickViewImageMain(page);
      await foHummingbirdModalQuickViewPage.selectThumbImage(page, 2);
      const secondCoverImageURL = await foHummingbirdModalQuickViewPage.getQuickViewImageMain(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });

    it('should display the first image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayFirstImage', baseContext);

      const firstCoverImageURL = await foHummingbirdModalQuickViewPage.getQuickViewImageMain(page);
      await foHummingbirdModalQuickViewPage.selectThumbImage(page, 1);
      const secondCoverImageURL = await foHummingbirdModalQuickViewPage.getQuickViewImageMain(page);
      expect(firstCoverImageURL).to.not.equal(secondCoverImageURL);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_0`);

  // Post-condition : Delete created product
  deleteProductTest(productWith2Images, `${baseContext}_postTest_1`);
});
