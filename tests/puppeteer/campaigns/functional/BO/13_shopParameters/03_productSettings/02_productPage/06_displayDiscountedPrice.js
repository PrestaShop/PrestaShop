require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_displayDiscountedPrice';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const CartRulesPage = require('@pages/BO/catalog/discounts');
const CatalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const AddCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');
const ProductPage = require('@pages/FO/product');
const HomePage = require('@pages/FO/home');
// Importing data
const ProductFaker = require('@data/faker/product');
const PriceRuleFaker = require('@data/faker/catalogPriceRule');

let browser;
let page;
const productData = new ProductFaker({type: 'Standard product', quantity: 10});
const priceRuleData = new PriceRuleFaker({});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    cartRulesPage: new CartRulesPage(page),
    catalogPriceRulesPage: new CatalogPriceRulesPage(page),
    addCatalogPriceRulePage: new AddCatalogPriceRulePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
  };
};

describe('Enable/Disable display discounted price', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to products page
  loginCommon.loginBO();

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.discountsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.cartRulesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.cartRulesPage.pageTitle);
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);
    await this.pageObjects.cartRulesPage.goToCatalogPriceRulesTab();
    const pageTitle = await this.pageObjects.catalogPriceRulesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.catalogPriceRulesPage.pageTitle);
  });

  it('should create new catalog price rule', async function () {
    await this.pageObjects.addCatalogPriceRulePage.goToAddNewCatalogPriceRulePage();
    await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);
    const validationMessage = await this.pageObjects.addCatalogPriceRulePage.createEditCatalogPriceRule(priceRuleData);
    await expect(validationMessage).to.equal(this.pageObjects.addCatalogPriceRulePage.settingUpdatedMessage);
  });

  /*it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.action} Display unavailable product attributes on the product page`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DisplayUnavailableProductAttributes`,
        baseContext,
      );
      const result = await this.pageObjects.productSettingsPage.setDisplayUnavailableProductAttributesStatus(
        test.args.enable,
      );
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should check the unavailable product attributes in FO product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkUnavailableAttribute${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      page = await this.pageObjects.productSettingsPage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.homePage.changeLanguage('en');
      await this.pageObjects.homePage.searchProduct(productData.name);
      await this.pageObjects.searchResultsPage.goToProductPage(1);
      const sizeIsVisible = await this.pageObjects.productPage.isUnavailableProductSizeDisplayed(
        productData.combinations.Size[0],
      );
      await expect(sizeIsVisible).to.be.equal(test.args.enable);
      const colorIsVisible = await this.pageObjects.productPage.isUnavailableProductColorDisplayed(
        productData.combinations.Color[0],
      );
      await expect(colorIsVisible).to.be.equal(test.args.enable);
      page = await this.pageObjects.productPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
*/
  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.discountsLink,
    );
    const pageTitle = await this.pageObjects.cartRulesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.cartRulesPage.pageTitle);
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRuleTab', baseContext);
    await this.pageObjects.cartRulesPage.goToCatalogPriceRulesTab();
    const pageTitle = await this.pageObjects.catalogPriceRulesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.catalogPriceRulesPage.pageTitle);
  });

  it('should delete catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);
    const deleteTextResult = await this.pageObjects.catalogPriceRulesPage.deleteCataloGPriceRule(productData);
    await expect(deleteTextResult).to.equal(this.pageObjects.catalogPriceRulesPage.productDeletedSuccessfulMessage);
  });

});
