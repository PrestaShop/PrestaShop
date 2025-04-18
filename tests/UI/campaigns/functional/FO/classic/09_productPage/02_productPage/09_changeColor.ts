// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_changeColor';

describe('FO - Product page - Product page : Change Color', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let firstCoverImageURL: string | null;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it(`should search the product "${dataProducts.demo_1.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchDemo1', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_1.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo1', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_1.name);
  });

  it('should get the default color selected', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getDefaultColor', baseContext);

    firstCoverImageURL = await foClassicProductPage.getCoverImage(page);

    const selectedAttribute = await foClassicProductPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttribute).to.equal('White');
  });

  it('should check the selected attribute text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSelectedColor', baseContext);

    const variantText = await foClassicProductPage.getSelectedAttributeText(page, 2);
    expect(variantText).to.equal('Color: White');
  });

  it('should change the color and check the image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeColor', baseContext);

    await foClassicProductPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

    const selectedAttribute = await foClassicProductPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttribute).to.equal('Black');

    const newCoverImage = await foClassicProductPage.getCoverImage(page);
    expect(newCoverImage).to.not.equal(firstCoverImageURL);
  });

  it('should check the attribute text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSelectedColor2', baseContext);

    const variantText = await foClassicProductPage.getSelectedAttributeText(page, 2);
    expect(variantText).to.equal('Color: Black');
  });
});
