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

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const FOHomePage = require('@pages/FO/home');

// Import data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_enableDisableAccentedUrl';


let browserContext;
let page;

const productName = 'TESTURLÉ';
const productNameWithoutAccent = 'TESTURLE';

const productData = new ProductFaker({name: productName, type: 'Standard product'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    foHomePage: new FOHomePage(page),
  };
};

describe('Enable/Disable accented URL', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    await this.pageObjects.productsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should create a product that the name contains accented characters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createAccentedCharsProduct', baseContext);

    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  const tests = [
    {args: {action: 'enable', enable: true, productNameInURL: productName}},
    {args: {action: 'disable', enable: false, productNameInURL: productNameWithoutAccent}},
  ];

  tests.forEach((test) => {
    it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToSeoPageTo${test.args.action}`, baseContext);

      await this.pageObjects.addProductPage.goToSubMenu(
        this.pageObjects.addProductPage.shopParametersParentLink,
        this.pageObjects.addProductPage.trafficAndSeoLink,
      );

      const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
    });

    it(`should ${test.args.action} accented URL`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AccentedUrl`, baseContext);

      const result = await this.pageObjects.seoAndUrlsPage.enableDisableAccentedURL(test.args.enable);
      await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulSettingsUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfter${test.args.action}`, baseContext);

      await this.pageObjects.seoAndUrlsPage.goToSubMenu(
        this.pageObjects.seoAndUrlsPage.catalogParentLink,
        this.pageObjects.seoAndUrlsPage.productsLink,
      );

      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfter${test.args.action}`, baseContext);

      await this.pageObjects.productsPage.resetFilterCategory();
      const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by the created product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `filterProductTo${test.args.action}`, baseContext);

      await this.pageObjects.productsPage.filterProducts('name', productName);
      const textColumn = await this.pageObjects.productsPage.getProductNameFromList(1);
      await expect(textColumn).to.contains(productName);
    });

    it('should go to the created product page and reset the friendly url', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFriendlyURl${test.args.action}`, baseContext);

      await this.pageObjects.productsPage.goToProductPage(1);
      const pageTitle = await this.pageObjects.addProductPage.getPageTitle();

      await expect(pageTitle).to.contains(this.pageObjects.addProductPage.pageTitle);
      await this.pageObjects.addProductPage.resetURL();
    });

    it('should check the product URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkProductUrl${test.args.action}`, baseContext);

      // Go to product page in FO
      page = await this.pageObjects.addProductPage.previewProduct();
      this.pageObjects = await init();

      const url = await this.pageObjects.foHomePage.getCurrentURL();
      await expect(url).to.contains(test.args.productNameInURL.toLowerCase());

      // Go back to BO
      page = await this.pageObjects.foHomePage.closePage(browserContext, 0);
      this.pageObjects = await init();
    });
  });

  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });
});
