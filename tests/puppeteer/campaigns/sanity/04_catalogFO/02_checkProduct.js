require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
// Importing pages
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const ProductData = require('@data/FO/product');

let browser;
let page;

// creating pages objects in a function
const init = async function () {
  return {
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
  };
};

/*
  Open the FO home page
  Check the first product page
 */
describe('Check the Product page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Steps
  it('should open the shop page', async function () {
    await this.pageObjects.homePage.goTo(global.FO.URL);
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
  });

  it('should go to the first product page', async function () {
    await this.pageObjects.homePage.goToProductPage('1');
    const pageTitle = await this.pageObjects.productPage.getPageTitle();
    await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
  });

  it('should check the product page', async function () {
    const result = await this.pageObjects.productPage.checkProduct(ProductData.firstProductData);
    await Promise.all([
      expect(result.name).to.be.true,
      expect(result.price).to.be.true,
      expect(result.quantity_wanted).to.be.true,
      expect(result.description).to.be.true,
    ]);
  });
});
