const helper = require('../../utils/helpers');
// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const ProductData = require('../../data/FO/product');

let browser;
let page;
let homePage;
let productPage;

// creating pages objects in a function
const init = async function () {
  await page.setExtraHTTPHeaders({
    'Accept-Language': 'en-GB',
  });
  homePage = await (new HomePage(page));
  productPage = await (new ProductPage(page));
};

/*
  Open the FO home page
  Check the first product page
 */
describe('Check the Product page', async () => {
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await init();
  });
  after(async () => {
    await browser.close();
  });
  it('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  it('should go to the first product page', async () => {
    await homePage.goToProductPage('1');
  });
  it('should check the product page', async () => {
    await productPage.checkProduct(ProductData.firstProductData);
  });
});
