// Using chai
const {expect} = require('chai');
const helper = require('../../utils/helpers');

// Importing pages
const HomePage = require('../../../pages/FO/home');

let browser;
let page;
let allProductsNumber = 0;

// creating pages objects in a function
const init = async function () {
  return {
    homePage: new HomePage(page),
  };
};

/*
  Open the FO home page
  Get the product number
  Filter products by a category
  Filter products by a subcategory
 */
describe('Filter Products by categories in Home page', async () => {
  // before and after functions
  before(async () => {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  it('should open the shop page', async () => {
    await this.pageObjects.homePage.goTo(global.URL_FO);
    await this.pageObjects.homePage.checkHomePage();
  });
  it('should check and get the products number', async () => {
    await this.pageObjects.homePage.waitForSelectorAndClick(this.pageObjects.homePage.allProductLink);
    allProductsNumber = await this.pageObjects.homePage.getNumberFromText(this.pageObjects.homePage.totalProducts);
    await expect(allProductsNumber).to.be.above(0);
  });
  it('should filter products by the category "Accessories" and check result', async () => {
    await this.pageObjects.homePage.filterByCategory('6');
    const numberOfProducts = await this.pageObjects.homePage.getNumberFromText(this.pageObjects.homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
  it('should filter products by the subcategory "Stationery" and check result', async () => {
    await this.pageObjects.homePage.filterSubCategory('6', '7');
    const numberOfProducts = await this.pageObjects.homePage.getNumberFromText(this.pageObjects.homePage.totalProducts);
    await expect(numberOfProducts).to.be.below(allProductsNumber);
  });
});
