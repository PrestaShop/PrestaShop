require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_enableDisableForceUpdateFriendlyURL';
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
const productData = new ProductFaker({type: 'Standard product'});
const editProductData = new ProductFaker({name: 'testForceFriendlyURL', type: 'Standard product'});

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
Enable force update friendly URL
Create then edit product
Check that the friendly URL is updated successfully
Disable force update friendly URL
 */
describe('Enable/Disable force update friendly URL', async () => {
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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPageToEnableUpdateFURL', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  it('should enable force update friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableForceUpdateFriendlyURL', baseContext);
    const result = await this.pageObjects.productSettingsPage.setForceUpdateFriendlyURL(true);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);
    await this.pageObjects.productsPage.goToAddProductPage();
    const validationMessage = await this.pageObjects.addProductPage.createEditProduct(productData, false);
    await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should update the product name and check that the friendly URL is updated without reset', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'UpdateProductAndCheckFriendlyURL', baseContext);
    const validationMessage = await this.pageObjects.addProductPage.createEditProduct(editProductData);
    await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
    const friendlyURL = await this.pageObjects.addProductPage.getFriendlyURL();
    await expect(friendlyURL).to.equal(editProductData.name.toLowerCase());
  });

  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPageToDisableUpdateFURL', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  it('should disable force update friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableForceUpdateFriendlyURL', baseContext);
    const result = await this.pageObjects.productSettingsPage.setForceUpdateFriendlyURL(true);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });
});
