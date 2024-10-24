// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_homePage_selectColor';

describe('FO - Home Page : Select color on hover on product list', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.equal(true);
  });

  it('should put the mouse over the first product and check that quick view button is displayed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'putMouseOnFirstProduct', baseContext);

    const isQuickViewLinkVisible = await foClassicHomePage.isQuickViewLinkVisible(page, 1);
    expect(isQuickViewLinkVisible).to.equal(true);

    const isBoxesVisible = await foClassicHomePage.isColoredBoxesVisible(page, 1);
    expect(isBoxesVisible).to.equal(true);
  });

  it('should select the color White for the first product in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectColor1', baseContext);

    await foClassicHomePage.selectProductColor(page, 1, 'White');

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_1.name);
  });

  it('should check that the displayed product is white', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProduct', baseContext);

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36356
    if (global.INSTALL.DB_SERVER === 'mariadb') {
      this.skip();
    }

    const pageURL = await foClassicProductPage.getCurrentURL(page);
    expect(pageURL).to.contains('color-white')
      .and.to.contains('size-m');
  });

  it('should go to Home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    await foClassicProductPage.goToHomePage(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Home page is not displayed').to.eq(true);
  });

  it('should select the color Black for the first product in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectColor2', baseContext);

    await foClassicHomePage.selectProductColor(page, 1, 'Black');

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_1.name);
  });

  it('should check that the displayed product is black', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProduct2', baseContext);

    const pageURL = await foClassicProductPage.getCurrentURL(page);
    expect(pageURL).to.contains('color-black')
      .and.to.contains('size-s');
  });
});
