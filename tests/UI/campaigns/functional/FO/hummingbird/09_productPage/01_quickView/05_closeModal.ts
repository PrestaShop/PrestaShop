// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_productPage_quickView_closeModal';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Quick view third product
- Close quick view modal
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page - Quick view : Close quick view modal', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('../../admin-dev/hummingbird.zip');
  });

  describe(`Display of the product '${Products.demo_6.name}'`, async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await homePage.quickViewProduct(page, 3);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click outside the modal and check that the modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOutSideModal', baseContext);

      const isQuickViewModalClosed = await quickViewModal.closeQuickViewModal(page, true);
      expect(isQuickViewModalClosed).to.equal(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView2', baseContext);

      await homePage.quickViewProduct(page, 3);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click on the cross link and check that the modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCrossLink', baseContext);

      const isQuickViewModalClosed = await quickViewModal.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
