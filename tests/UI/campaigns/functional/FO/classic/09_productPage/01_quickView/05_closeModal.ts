// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  foClassicHomePage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

const baseContext: string = 'functional_FO_classic_productPage_quickView_closeModal';

/*
Pre-condition
- Enable the theme classic
Scenario:
- Go to FO
- Quick view third product
- Close quick view modal
Post-condition
- Disable the theme classic
 */
describe('FO - Product page - Quick view : Close quick view modal', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Enable the theme classic
  enableTheme('classic', `${baseContext}_preTest_0`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Close quick view modal', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await foClassicHomePage.quickViewProduct(page, 3);

      const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click outside the modal and check that the modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOutSideModal', baseContext);

      const isQuickViewModalClosed = await foClassicModalQuickViewPage.closeQuickViewModal(page, true);
      expect(isQuickViewModalClosed).to.equal(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView2', baseContext);

      await foClassicHomePage.quickViewProduct(page, 3);

      const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should click on the cross link and check that the modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCrossLink', baseContext);

      const isQuickViewModalClosed = await foClassicModalQuickViewPage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.equal(true);
    });
  });

  // Post-condition : Disable the theme classic
  disableTheme('classic', `${baseContext}_postTest`);
});
