// Using chai
const {expect} = require('chai');

// Importing pages
const HomePage = require('../../../pages/FO/home');

let page;
let homePage;
let allProductsNumber = 0;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  homePage = await (new HomePage(page));
};

/*
  Open the FO home page
  Get the product number
  Filter products by a category
  Filter products by a subcategory
 */
global.scenario('Filter Products by categories in Home page', () => {
  test('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should check and get the products number', async () => {
    await homePage.waitForSelectorAndClick(homePage.allProductLink);
    allProductsNumber = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(allProductsNumber).to.be.above(0);
  });
  test('should filter products by the category "Accessories" and check result', async () => {
    await homePage.filterByCategory('6');
    const numberOfProducts = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
  test('should filter products by the subcategory "Stationery" and check result', async () => {
    await homePage.filterSubCategory('6', '7');
    const numberOfProducts = await homePage.getNumberFromText(homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
}, init, true);
