/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_CRUDStandardProductWithCombinationsInBO';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const FOProductPage = require('@pages/FO/product');
const ProductFaker = require('@data/faker/product');

let browserContext;
let page;
let productWithCombinations;
let editedProductWithCombinations;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    foProductPage: new FOProductPage(page),
  };
};
// Create, read, update and delete Standard product with combinations in BO
describe('Create, read, update and delete Standard product with combinations in BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
    const productToCreate = {
      type: 'Standard product',
      productHasCombinations: true,
    };
    productWithCombinations = await (new ProductFaker(productToCreate));
    editedProductWithCombinations = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });
  // Steps
  loginCommon.loginBO();

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    await this.pageObjects.productsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create Product with Combinations', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);
    await this.pageObjects.productsPage.goToAddProductPage();
    await this.pageObjects.addProductPage.createEditBasicProduct(productWithCombinations);
    const createProductMessage = await this.pageObjects.addProductPage.setCombinationsInProduct(
      productWithCombinations,
    );
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);
    page = await this.pageObjects.addProductPage.previewProduct();
    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.getProductInformation(productWithCombinations);
    page = await this.pageObjects.foProductPage.closePage(browserContext, 0);
    this.pageObjects = await init();
    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(productWithCombinations.name),
      expect(result.price).to.equal(productWithCombinations.price),
      expect(result.description).to.contains(productWithCombinations.description),
    ]);
  });

  it('should edit Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);
    await this.pageObjects.addProductPage.createEditBasicProduct(editedProductWithCombinations);
    const createProductMessage = await this.pageObjects.addProductPage.setCombinationsInProduct(
      editedProductWithCombinations,
    );
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);
    page = await this.pageObjects.addProductPage.previewProduct();

    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.getProductInformation(editedProductWithCombinations);
    page = await this.pageObjects.foProductPage.closePage(browserContext, 0);
    this.pageObjects = await init();
    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(editedProductWithCombinations.name),
      expect(result.price).to.equal(editedProductWithCombinations.price),
      expect(result.description).to.contains(editedProductWithCombinations.description),
    ]);
  });

  it('should delete Product and be on product list page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });
});
