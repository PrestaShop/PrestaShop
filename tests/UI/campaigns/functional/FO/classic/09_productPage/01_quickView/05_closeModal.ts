// Import utils
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_quickView_closeModal';

/*
Scenario:
- Go to FO
- Quick view third product
- Close quick view modal
 */
describe('FO - Product page - Quick view : Close quick view modal', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

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
