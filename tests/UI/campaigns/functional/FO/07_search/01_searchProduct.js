require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_search_searchProduct';

let browserContext;
let page;

/*
Go to FO
Search Product
 */

describe('FO - Search Page : Search product', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should search product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await homePage.searchProduct(page, Products.demo_8.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });
});
