require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_shopParameters_productSettings_maxSizeShortDescription';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
// Importing data
const ProductFaker = require('@data/faker/product');

let browser;
let page;
const productData = new ProductFaker({type: 'Standard product', status: false});
const maxSummarySizeValue = 5;
const defaultSummarySizeValue = 800;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
  };
};

/*
Update max size of short description to 5
Check the error message when the description size is more than 5 characters
Go back to the default max size short description
Check the validation message when the description is less than 800 characters
 */
describe('Update max size of short description', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  const tests = [
    {args: {descriptionSize: maxSummarySizeValue}},
    {args: {descriptionSize: defaultSummarySizeValue}},
  ];
  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index + 1}`, baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.productSettingsLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
    });

    it(`should update max size of short description to ${test.args.descriptionSize}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateMaxSizeSummaryValue${index + 1}`, baseContext);
      const result = await this.pageObjects.productSettingsPage.UpdateMaxSizeOfSummary(test.args.descriptionSize);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToCatalogProductsPage${index + 1}`, baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.productsLink,
      );
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });
    if (test.args.descriptionSize === maxSummarySizeValue) {
      it(`should create a product with a summary more than ${test.args.descriptionSize} characters
      and check the error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index + 1}`, baseContext);
        await this.pageObjects.productsPage.goToAddProductPage();
        let errorMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
        await expect(errorMessage).to.equal(this.pageObjects.addProductPage.errorMessage);
        errorMessage = await this.pageObjects.addProductPage.getErrorMessageWhenSummaryIsTooLong();
        await expect(errorMessage).to.equal(
          this.pageObjects.addProductPage.errorMessageWhenSummaryTooLong.replace('%NUMBER', maxSummarySizeValue),
        );
      });
    } else {
      it(`should create a product with a summary less than ${test.args.descriptionSize} characters`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index + 1}`, baseContext);
        await this.pageObjects.productsPage.goToAddProductPage();
        const validationMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
        await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    }
  });

  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });
});
