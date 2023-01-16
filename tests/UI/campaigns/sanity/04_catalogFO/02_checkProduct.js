// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import homePage from '@pages/FO/home';
import productPage from '@pages/FO/product';

require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import data
const {Products} = require('@data/demo/products');

const baseContext = 'sanity_catalogFO_checkProduct';

let browserContext;
let page;

/*
  Open the FO home page
  Check the first product page
 */
describe('FO - Catalog : Check the Product page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Steps
  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await homePage.goTo(page, global.FO.URL);
    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should go to the first product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await homePage.goToProductPage(page, 1);
    const pageTitle = await productPage.getPageTitle(page);
    await expect(pageTitle).to.contains(Products.demo_1.name);
  });

  it('should check the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductPage', baseContext);

    const result = await productPage.getProductInformation(page);
    await Promise.all([
      expect(result.name).to.equal(Products.demo_1.name),
      expect(result.price).to.equal(Products.demo_1.finalPrice),
      expect(result.description).to.contains(Products.demo_1.description),
    ]);
  });
});
