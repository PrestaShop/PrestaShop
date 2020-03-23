require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_forceUpdateFriendlyURL';
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
const editProductData = new ProductFaker({name: 'testForceFriendlyURL', type: 'Standard product', status: false});

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

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);
    await this.pageObjects.productsPage.goToAddProductPage();
    const validationMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
    await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });
  const tests = [
    {
      args:
        {
          action: 'enable', enable: true, editProduct: editProductData, friendlyURL: editProductData.name,
        },
    },
    {
      args:
        {
          action: 'disable', enable: false, editProduct: productData, friendlyURL: editProductData.name,
        },
    },
  ];
  tests.forEach((test) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToProductSettingsPageTo${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}FriendlyURL`,
        baseContext,
      );
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.productSettingsLink,
      );
      const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
    });

    it(`should ${test.args.action} force update friendly URL`, async function () {
      await testContext.addContextItem(this,
        'testIdentifier',
        `${test.args.action}ForceUpdateFriendlyURL`,
        baseContext,
      );
      const result = await this.pageObjects.productSettingsPage.setForceUpdateFriendlyURLStatus(test.args.enable);
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

    it('should go to the created product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToProductPageToCheckFriendlyURL${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      await this.pageObjects.productsPage.resetFilter();
      await this.pageObjects.productsPage.goToProductPage(1);
      const pageTitle = await this.pageObjects.addProductPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProductPage.pageTitle);
    });

    it('should update the product name and check the friendly URL', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `UpdateProductAndCheckFriendlyURL${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      const validationMessage = await this.pageObjects.addProductPage.createEditBasicProduct(test.args.editProduct);
      await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      const friendlyURL = await this.pageObjects.addProductPage.getFriendlyURL();
      await expect(friendlyURL).to.equal(test.args.friendlyURL.toLowerCase());
    });
  });
  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });
});
