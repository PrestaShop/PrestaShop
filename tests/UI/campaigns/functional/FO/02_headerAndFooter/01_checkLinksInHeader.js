require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const contactUsPage = require('@pages/FO/contactUs');
const cartPage = require('@pages/FO/cart');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Categories} = require('@data/demo/categories');
const {Products} = require('@data/demo/products');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_checkLinksInHeader';

let browserContext;
let page;

/*
Go to FO
Check header links:
- Contact us
- Language (English, Français)
- Sign in
- My account
- Sign out
- Cart
- Logo
- Categories and subcategories
- Search
 */

describe('Check links in header page', async () => {
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

  it('should check \'Contact us\' header links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContactUsHeaderLink', baseContext);

    // Check Contact us
    await homePage.goToHeaderLink(page, 'Contact us');

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle, 'Fail to open FO login page').to.contains(contactUsPage.pageTitle);
  });

  it('check languages link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLanguagesLink', baseContext);

    await homePage.changeLanguage(page, 'fr');

    let language = await homePage.getShopLanguage(page);
    expect(language).to.equal('Français');

    await homePage.changeLanguage(page, 'en');

    language = await homePage.getShopLanguage(page);
    expect(language).to.equal('English');
  });

  it('should check sign in link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignInLink', baseContext);

    // Check sign in link
    await homePage.goToHeaderLink(page, 'Sign in');

    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);

    // Sign in
    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should check sign out link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSignOutLink', baseContext);

    // Sign out
    await loginPage.logout(page);

    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should check shopping cart link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartLink', baseContext);

    await loginPage.goToHomePage(page);

    // Add product to cart by quick view
    await homePage.addProductToCartByQuickView(page, 1, 1);

    // Close block cart modal
    const isQuickViewModalClosed = await homePage.closeBlockCartModal(page);
    await expect(isQuickViewModalClosed).to.be.true;

    // Check cart link
    await homePage.goToHeaderLink(page, 'Cart');

    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should check logo link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

    await homePage.goToHeaderLink(page, 'Logo');

    const pageTitle = await homePage.getPageTitle(page);
    await expect(pageTitle).to.equal(homePage.pageTitle);
  });

  [Categories.clothes, Categories.accessories, Categories.art].forEach((test) => {
    it(`should check category '${test.name}' link`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `check${test.name}Link`, baseContext);

      await homePage.goToCategory(page, test.id);

      const pageTitle = await homePage.getPageTitle(page);
      await expect(pageTitle).to.equal(test.name);
    });
  });

  [
    {args: {category: Categories.clothes, subcategory: Categories.men}},
    {args: {category: Categories.clothes, subcategory: Categories.women}},
    {args: {category: Categories.accessories, subcategory: Categories.stationery}},
    {args: {category: Categories.accessories, subcategory: Categories.homeAccessories}},
  ].forEach((test) => {
    it(`should check subcategory '${test.args.subcategory.name}' link`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `check${test.args.category.name}Link`, baseContext);

      await homePage.goToSubCategory(page, test.args.category.id, test.args.subcategory.id);

      const pageTitle = await homePage.getPageTitle(page);
      await expect(pageTitle).to.equal(test.args.subcategory.name);
    });
  });

  it('should check search link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSearchLink', baseContext);

    await homePage.searchProduct(page, Products.demo_8.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });
});
