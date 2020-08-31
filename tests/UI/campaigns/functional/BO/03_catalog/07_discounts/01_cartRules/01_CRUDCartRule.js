require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const ProductData = require('@data/FO/product');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_CRUDCartRule';

let browserContext;
let page;

const newCartRuleData = new CartRuleFaker(
  {
    code: '4QABV6L3',
    customer: 'pub@prestashop.com',
    percent: true,
    value: 20,
  },
);

const editCartRuleData = new CartRuleFaker(
  {
    customer: 'pub@prestashop.com',
    percent: true,
    value: 30,
  },
);

describe('CRUD cart rule', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  describe('Create cart rule', async () => {
    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);
      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });
    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('Add product to cart and check cart rule', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);
      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page);

      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await cartPage.getNumberFromText(page, foProductPage.cartProductsCount);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPromoCode', baseContext);

      await cartPage.setPromoCode(page, newCartRuleData.code);
    });
  });

  describe('Update cart rule', async () => {
    it('should go to edit cart rule page', async function () {

    });

    it('should update cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, editCartRuleData);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });
});
