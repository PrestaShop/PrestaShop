// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_changeColor';

describe('FO - Product page - Product page : Change Color', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let firstCoverImageURL: string | null;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it(`should search the product "${dataProducts.demo_1.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchDemo1', baseContext);

    await homePage.searchProduct(page, dataProducts.demo_1.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo1', baseContext);

    await searchResultsPage.goToProductPage(page, 1);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_1.name);
  });

  it('should get the default color selected', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getDefaultColor', baseContext);

    firstCoverImageURL = await productPage.getCoverImage(page);

    const selectedAttribute = await productPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttribute).to.equal('White');
  });

  it('should check the selected attribute text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSelectedColor', baseContext);

    const variantText = await productPage.getSelectedAttributeText(page, 2);
    expect(variantText).to.equal('Color: White');
  });

  it('should change the color and check the image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeColor', baseContext);

    await productPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

    const selectedAttribute = await productPage.getSelectedAttribute(page, 2, 'radio');
    expect(selectedAttribute).to.equal('Black');

    const newCoverImage = await productPage.getCoverImage(page);
    expect(newCoverImage).to.not.equal(firstCoverImageURL);
  });

  it('should check the attribute text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSelectedColor2', baseContext);

    const variantText = await productPage.getSelectedAttributeText(page, 2);
    expect(variantText).to.equal('Color: Black');
  });
});
