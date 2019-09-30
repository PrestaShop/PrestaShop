const helper = require('../../utils/helpers');
// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const ProductData = require('../../data/FO/product');

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
    await this.pageObjects.homePage.checkHomePage();
  });
  it('should go to the first product page', async function () {
    await this.pageObjects.homePage.goToProductPage('1');
  });
  it('should check the product page', async function () {
    await this.pageObjects.productPage.checkProduct(ProductData.firstProductData);
  });
});
