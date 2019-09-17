const {expect} = require('chai');
const helper = require('../../utils/helpers');
// Using chai

// Importing pages
const HomePage = require('../../../pages/FO/home');

let browser;
let page;
let homePage;
let allProductsNumber = 0;

// creating pages objects in a function
const init = async function () {
  homePage = await (new HomePage(page));
};

/*
  Open the FO home page
  Get the product number
  Filter products by a category
  Filter products by a subcategory
 */
describe('Filter Products by categories in Home page', async () => {
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
  it('should check and get the products number', async () => {
    await homePage.waitForSelectorAndClick(homePage.allProductLink);
    allProductsNumber = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(allProductsNumber).to.be.above(0);
  });
  it('should filter products by the category "Accessories" and check result', async () => {
    await homePage.filterByCategory('6');
    const numberOfProducts = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
  it('should filter products by the subcategory "Stationery" and check result', async () => {
    await homePage.filterSubCategory('6', '7');
    const numberOfProducts = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
});
