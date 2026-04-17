import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataProducts,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_information_withAndWithoutCode';

describe('BO - Catalog - Cart rules : CRUD cart rule with/without code', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const cartRuleWithoutCode: FakerCartRule = new FakerCartRule({
    dateFrom: pastDate,
    name: 'withoutCode',
    discountType: 'Percent',
    discountPercent: 20,
  });
  const cartRuleWithCode: FakerCartRule = new FakerCartRule({
    name: 'withCodeHighlightFalse',
    code: '4QABV6L3',
    highlight: false,
    discountType: 'Percent',
    discountPercent: 20,
  });
  const secondCartRuleWithCode: FakerCartRule = new FakerCartRule({
    name: 'withCodeHighlightTrue',
    code: '4QABU4OP',
    highlight: true,
    discountType: 'Percent',
    discountPercent: 20,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.discountsLink,
    );

    const pageTitle = await boCartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
  });

  describe('case 1 : Create cart rule without code then check it on FO', async () => {
    describe('Create cart rule on BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleWithoutCode);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

        // View my shop and init pages
        page = await boCartRulesCreatePage.viewMyShop(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await foHummingbirdHomePage.goToProductPage(page, 1);

        const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foHummingbirdProductPage.addProductToTheCart(page);

        const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const discountPercent = cartRuleWithoutCode.getDiscountPercent();
        const totalAfterDiscount = dataProducts.demo_1.finalPrice
          - ((dataProducts.demo_1.finalPrice * discountPercent) / 100);

        const priceATI = await foHummingbirdCartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterDiscount.toFixed(2)));

        const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
        expect(cartRuleName).to.contains(cartRuleWithoutCode.name);

        const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
        expect(discountValue).to.equal(
          `-€${Math.abs(parseFloat(totalAfterDiscount.toFixed(2)) - dataProducts.demo_1.finalPrice)}`,
        );
      });

      it('should remove product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

        await foHummingbirdCartPage.deleteProduct(page, 1);

        const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(0);
      });
    });
  });

  describe('case 2 : Create cart rule with code, highlight disabled then check it on FO', async () => {
    describe('Update cart rule on BO', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should go to edit cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

        await boCartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
      });

      it('should update cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleWithCode);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        // View my shop and init pages
        page = await boCartRulesCreatePage.viewMyShop(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await foHummingbirdHomePage.goToProductPage(page, 1);

        const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foHummingbirdProductPage.addProductToTheCart(page);

        const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(1);
      });

      it('should verify the total before the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalBeforeDiscount', baseContext);

        const priceATI = await foHummingbirdCartPage.getATIPrice(page);
        expect(priceATI).to.equal(dataProducts.demo_1.finalPrice);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

        await foHummingbirdCartPage.addPromoCode(page, cartRuleWithCode.code);

        const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
        expect(cartRuleName).to.contains(cartRuleWithCode.name);
      });

      it('should verify the total after the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount2', baseContext);

        const discountPercent = cartRuleWithoutCode.getDiscountPercent();
        const totalAfterPromoCode = dataProducts.demo_1.finalPrice
          - ((dataProducts.demo_1.finalPrice * discountPercent) / 100);

        const priceATI = await foHummingbirdCartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

        const discountValue = await foHummingbirdCartPage.getCartRuleValue(page, 1);
        expect(discountValue).to.contains(
          `-€${Math.abs(parseFloat((totalAfterPromoCode - dataProducts.demo_1.finalPrice).toFixed(2)))}`,
        );
      });

      it('should remove voucher and product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProductAndVoucher', baseContext);

        await foHummingbirdCartPage.removeVoucher(page, 1);
        await foHummingbirdCartPage.deleteProduct(page, 1);

        const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(0);
      });
    });
  });

  describe('case 3 : Create cart rule with code, highlight enabled then check it on FO', async () => {
    describe('Update cart rule on BO', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should go to edit cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage2', baseContext);

        await boCartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
      });

      it('should update cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule2', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, secondCartRuleWithCode);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop3', baseContext);

        // View my shop and init pages
        page = await boCartRulesCreatePage.viewMyShop(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage3', baseContext);

        await foHummingbirdHomePage.goToProductPage(page, 1);

        const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart3', baseContext);

        await foHummingbirdProductPage.addProductToTheCart(page);

        const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(1);
      });

      it('should verify the total before the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalBeforeDiscount2', baseContext);

        const priceATI = await foHummingbirdCartPage.getATIPrice(page);
        expect(priceATI).to.equal(dataProducts.demo_1.finalPrice);
      });

      it('should check the displayed promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock2', baseContext);

        const promoCode = await foHummingbirdCartPage.getHighlightPromoCode(page);
        expect(promoCode).to.equal(`${secondCartRuleWithCode.code} - ${secondCartRuleWithCode.name}`);
      });

      it('should click on the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

        await foHummingbirdCartPage.clickOnPromoCode(page);

        const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
        expect(cartRuleName).to.contains(secondCartRuleWithCode.name);
      });

      it('should verify the total after the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

        const totalAfterPromoCode = dataProducts.demo_1.finalPrice
          - ((dataProducts.demo_1.finalPrice * cartRuleWithCode.getDiscountPercent()) / 100);

        const priceATI = await foHummingbirdCartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

        const discountValue = await foHummingbirdCartPage.getCartRuleValue(page, 1);
        expect(discountValue).to.contains(
          `-€${Math.abs(parseFloat((totalAfterPromoCode - dataProducts.demo_1.finalPrice).toFixed(2)))}`,
        );
      });

      it('should remove voucher and product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProductAndVoucher2', baseContext);

        await foHummingbirdCartPage.removeVoucher(page, 1);
        await foHummingbirdCartPage.deleteProduct(page, 1);

        const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(0);
      });
    });
  });

  describe('Delete the updated cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await boCartRulesPage.deleteCartRule(page);
      expect(validationMessage).to.contains(boCartRulesPage.successfulDeleteMessage);
    });
  });
});
