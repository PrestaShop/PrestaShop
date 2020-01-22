require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const FOBasePage = require('@pages/FO/FObasePage');
// Importing data
const ProductFaker = require('@data/faker/product');

let browser;
let page;
const productName = 'testURLé';
const productNameWithoutAccent = 'testURLe';
const productData = new ProductFaker({name: productName, type: 'Standard product', productHasCombinations: false});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    foBasePage: new FOBasePage(page),
  };
};

describe('Enable/Disable accented URL', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Catalog > Products\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should create a product that the name contains accented characters', async function () {
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(productData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  const tests = [
    {args: {action: 'enable', enable: true, productNameInURL: productName}},
    {args: {action: 'disable', enable: false, productNameInURL: productNameWithoutAccent}},
  ];

  tests.forEach((test) => {
    it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.trafficAndSeoLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
    });

    it(`should ${test.args.action} accented URL`, async function () {
      const result = await this.pageObjects.seoAndUrlsPage.enableDisableAccentedURL(test.args.enable);
      await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.productsLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await this.pageObjects.productsPage.resetFilterCategory();
      const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by the created product name', async function () {
      await this.pageObjects.productsPage.filterProducts('name', productName);
      const textColumn = await this.pageObjects.productsPage.getProductNameFromList(1);
      await expect(textColumn).to.contains(productName);
    });

    it('should go to the created product page and reset the friendly url', async function () {
      await this.pageObjects.productsPage.goToProductPage(1);
      const pageTitle = await this.pageObjects.addProductPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProductPage.pageTitle);
      await this.pageObjects.addProductPage.resetURL();
    });

    it('should check the product URL', async function () {
      page = await this.pageObjects.addProductPage.previewProduct();
      this.pageObjects = await init();
      const url = await this.pageObjects.foBasePage.getCurrentURL();
      await expect(url).to.contains(test.args.productNameInURL.toLowerCase());
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  it('should delete product', async function () {
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });
});
