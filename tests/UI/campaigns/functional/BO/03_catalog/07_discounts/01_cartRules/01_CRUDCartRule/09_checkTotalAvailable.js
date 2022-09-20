require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const {getDateFormat} = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import common tests
const {deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule.js');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const foLoginPage = require('@pages/FO/login');
const checkLogin = require('@pages/FO/checkout');
const {choosePaymentAndOrder} = require('@pages/FO/checkout');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const ProductData = require('@data/FO/product');
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');
// const {paymentType} = require('@data/demo/paymentMethods');
// paymentMethods.wirePayment,


// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_withAndWithoutCode';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
const pastDate = getDateFormat('yyyy-mm-dd', 'past');

const cartRuleCode = new CartRuleFaker(
  {
    dateFrom: pastDate,
    name: 'addCartRuleName',
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
  },
);

const newCartRuleCode = new CartRuleFaker(
  {
    code: '4QABV6L3',
    customer: DefaultCustomer.email,
    discountType: 'Percent',
    discountPercent: 20,
  },
);

describe('BO - Catalog - Cart rules', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create a Cart rules', async () => {

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
  });

  describe('Catalog - Create a new discount', async () => {

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleCode);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('Verify discount on FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_2', baseContext);

      await cartPage.addPromoCode(page, cartRuleCode.code);
    });

    it('should verify the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount_1', baseContext);

      const discountedPrice = Products.demo_1.finalPrice
        - (Products.demo_1.finalPrice * newCartRuleCode.discountPercent / 100);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(parseFloat(discountedPrice.toFixed(2)));

    });

    it('should proceed to checkouts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);
      await page.waitForTimeout(10000)
    });

    it('should checkout by signIn', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO_1', baseContext);

      await checkLogin.clickOnSignIn(page);

      const isCustomerConnected = await checkLogin.isCustomerConnected(page);
      await checkLogin.customerLogin(page, DefaultCustomer);

      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should confirm adress after signIn', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAdressStep', baseContext);

      await checkLogin.goToDeliveryStep(page);
    });

    it('should choose the shipping method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'shippingMethodStep', baseContext);

      await checkLogin.goToPaymentStep(page);
    });

    it('should choose the payment type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'paymentTypeStep', baseContext);

      await checkLogin.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
    });
  });

  //post condition : delete cart rule
  deleteCartRuleTest(cartRuleCode.name, baseContext);
});
