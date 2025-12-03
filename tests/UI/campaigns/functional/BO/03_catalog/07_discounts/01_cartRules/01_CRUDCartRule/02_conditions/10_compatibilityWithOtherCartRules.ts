import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boPaymentPreferencesPage,
  type BrowserContext,
  dataAddresses,
  dataCarriers,
  dataCountries,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCartRule,
  foClassicCartPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyAddressesCreatePage,
  foClassicMyAddressesPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
  utilsDate,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_compatibilityWithOtherCartRules';

describe('BO - Cart rules - Conditions : Case 9 - Compatibility with other cart rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numCartRules: number = 0;

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const cartRuleTestAmount: FakerCartRule = new FakerCartRule({
    name: 'Test Amount',
    code: 'AMOUNT',
    quantity: 100,
    quantityPerUser: 100,
    discountType: 'Amount',
    discountAmount: {
      value: 5,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
    dateFrom: pastDate,
    dateTo: futureDate,
  });
  const cartRuleTestFreeShipping: FakerCartRule = new FakerCartRule({
    name: 'Test Free Shipping',
    code: 'SHIPPING',
    quantity: 100,
    quantityPerUser: 100,
    freeShipping: true,
    dateFrom: pastDate,
    dateTo: futureDate,
  });
  const cartRuleTestFreeGift: FakerCartRule = new FakerCartRule({
    name: 'Test Free Gift',
    code: 'GIFT',
    dateFrom: pastDate,
    dateTo: futureDate,
    freeGift: true,
    freeGiftProduct: dataProducts.demo_12,
  });
  const cartRuleTestPercent: FakerCartRule = new FakerCartRule({
    name: 'Test Percent',
    code: 'PERCENT',
    dateFrom: pastDate,
    dateTo: futureDate,
    discountType: 'Percent',
    discountPercent: 10,
  });
  const cartRuleTestPercentWODiscountedProducts: FakerCartRule = new FakerCartRule({
    name: 'Test Percent',
    code: 'PERCENT',
    dateFrom: pastDate,
    dateTo: futureDate,
    discountType: 'Percent',
    discountPercent: 10,
    excludeDiscountProducts: true,
  });
  const customerAddressUS: FakerAddress = new FakerAddress({
    alias: dataAddresses.address_2.alias,
    firstName: dataAddresses.address_2.firstName,
    lastName: dataAddresses.address_2.lastName,
    company: dataAddresses.address_2.company,
    address: dataAddresses.address_2.address,
    secondAddress: dataAddresses.address_2.secondAddress,
    postalCode: dataAddresses.address_2.postalCode,
    city: dataAddresses.address_2.city,
    state: 'Georgia',
    country: dataCountries.unitedStates.name,
    phone: dataAddresses.address_2.phone,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create cart rule', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Payment > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.paymentParentLink,
        boDashboardPage.preferencesLink,
      );
      await boPaymentPreferencesPage.closeSfToolBar(page);

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });

    it(`should check the ${dataCountries.unitedStates.name} country for '${dataPaymentMethods.wirePayment.moduleName}'`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enable', baseContext);

        const result = await boPaymentPreferencesPage.setCountryRestriction(
          page,
          dataCountries.unitedStates.id,
          dataPaymentMethods.wirePayment.moduleName,
          true,
        );
        expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
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

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numCartRules = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numCartRules).to.be.at.least(0);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it(`should create cart rule "${cartRuleTestAmount.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleTestAmount', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleTestAmount);
      expect(validationMessage).to.contains(boCartRulesPage.successfulCreationMessage);

      const numCartRulesAfterAdd = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numCartRulesAfterAdd).to.equal(numCartRules + 1);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it(`should create cart rule "${cartRuleTestFreeShipping.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleTestFreeShipping', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleTestFreeShipping);
      expect(validationMessage).to.contains(boCartRulesPage.successfulCreationMessage);

      const numCartRulesAfterAdd = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numCartRulesAfterAdd).to.equal(numCartRules + 2);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage3', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it(`should create cart rule "${cartRuleTestFreeGift.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleTestFreeGift', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleTestFreeGift);
      expect(validationMessage).to.contains(boCartRulesPage.successfulCreationMessage);

      const numCartRulesAfterAdd = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numCartRulesAfterAdd).to.equal(numCartRules + 3);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage4', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it(`should create cart rule "${cartRuleTestPercent.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleTestPercent', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleTestPercent);
      expect(validationMessage).to.contains(boCartRulesPage.successfulCreationMessage);

      const numCartRulesAfterAdd = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numCartRulesAfterAdd).to.equal(numCartRules + 4);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await boCartRulesPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to the "Your addresses" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesPage.pageTitle);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      const addressPosition = await foClassicMyAddressesPage.getAddressPosition(page, customerAddressUS.alias);
      await foClassicMyAddressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await foClassicMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesCreatePage.updateFormTitle);
    });

    it.skip('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await foClassicMyAddressesCreatePage.setAddress(page, customerAddressUS);
      expect(textResult).to.equal(foClassicMyAddressesPage.updateAddressSuccessfulMessage);
    });

    it.skip(`should search for the product '${dataProducts.demo_6.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it.skip('should add the product to cart and click on continue shopping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page, 10, undefined, true);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(10);
    });

    it.skip('should check amounts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAmounts', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10;

      const totalShipping = dataCarriers.myCarrier.price;

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping);
    });

    it.skip(`should add the promo code "${cartRuleTestPercent.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCodePercent', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleTestPercent.code);

      const cartRuleName = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.eq(cartRuleTestPercent.name);
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValuePercent', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscounts = await utilsCore.percentage(totalProducts, cartRuleTestPercent.getDiscountPercent());

      const discountValue = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue).to.equal(`-€${totalDiscounts.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it.skip(`should add the promo code "${cartRuleTestFreeGift.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCodeFreeGift', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleTestFreeGift.code);

      const cartRuleName1 = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName1).to.eq(cartRuleTestFreeGift.name);

      const cartRuleName2 = await foClassicCartPage.getCartRuleName(page, 2);
      expect(cartRuleName2).to.eq(cartRuleTestPercent.name);
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValueFreeGift', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${totalDiscountPercent.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it.skip(`should add the promo code "${cartRuleTestFreeShipping.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCodeFreeShipping', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleTestFreeShipping.code);

      const cartRuleName = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.eq(cartRuleTestFreeGift.name);

      const cartRuleName1 = await foClassicCartPage.getCartRuleName(page, 2);
      expect(cartRuleName1).to.eq(cartRuleTestFreeShipping.name);

      const cartRuleName2 = await foClassicCartPage.getCartRuleName(page, 3);
      expect(cartRuleName2).to.eq(cartRuleTestPercent.name);
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValueFreeShipping', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded + totalShipping;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal('Free shipping');

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal(`-€${totalDiscountPercent.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it.skip(`should add the promo code "${cartRuleTestAmount.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCodeAmount', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleTestAmount.code);

      const cartRuleName1 = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName1).to.eq(cartRuleTestFreeGift.name);

      const cartRuleName2 = await foClassicCartPage.getCartRuleName(page, 2);
      expect(cartRuleName2).to.eq(cartRuleTestAmount.name);

      const cartRuleName3 = await foClassicCartPage.getCartRuleName(page, 3);
      expect(cartRuleName3).to.eq(cartRuleTestFreeShipping.name);

      const cartRuleName4 = await foClassicCartPage.getCartRuleName(page, 4);
      expect(cartRuleName4).to.eq(cartRuleTestPercent.name);
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValueAmount', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountAmount = parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10);

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10 - totalDiscountAmount,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent
        + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded
        + totalShipping
        + totalDiscountAmount;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10).toFixed(2)}`);

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal('Free shipping');

      const discountValue4 = await foClassicCartPage.getCartRuleValue(page, 4);
      expect(discountValue4).to.equal(`-€${totalDiscountPercent.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it.skip(`should delete the promo code "${cartRuleTestPercent.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removePromoCodePercent', baseContext);

      await foClassicCartPage.removeVoucher(page, 4);

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10).toFixed(2)}`);

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal('Free shipping');
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValueWOPercent', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountAmount = parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10);

      const totalDiscounts = cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded + totalShipping + totalDiscountAmount;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10).toFixed(2)}`);

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal('Free shipping');

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it.skip(`should add the promo code "${cartRuleTestPercent.code}" and check the cart rule name`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCodePercentBis', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleTestPercent.code);

      const cartRuleName1 = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName1).to.eq(cartRuleTestFreeGift.name);

      const cartRuleName2 = await foClassicCartPage.getCartRuleName(page, 2);
      expect(cartRuleName2).to.eq(cartRuleTestAmount.name);

      const cartRuleName3 = await foClassicCartPage.getCartRuleName(page, 3);
      expect(cartRuleName3).to.eq(cartRuleTestFreeShipping.name);

      const cartRuleName4 = await foClassicCartPage.getCartRuleName(page, 4);
      expect(cartRuleName4).to.eq(cartRuleTestPercent.name);
    });

    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValuePercent2', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountAmount = parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10);

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10 - totalDiscountAmount,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent
        + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded
        + totalShipping
        + totalDiscountAmount;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10).toFixed(2)}`);

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal('Free shipping');

      const discountValue4 = await foClassicCartPage.getCartRuleValue(page, 4);
      expect(discountValue4).to.equal(`-€${totalDiscountPercent.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });
  });

  describe.skip('BO : Modify cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBackBO', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

      await boCartRulesPage.filterCartRules(page, 'input', 'name', cartRuleTestPercent.name);

      const numberOfCartRulesAfterFilter = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numberOfCartRulesAfterFilter).to.equal(1);
    });

    it('should go to edit cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

      await boCartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleTestPercentWODiscountedProducts);
      expect(validationMessage).to.contains(boCartRulesPage.successfulUpdateMessage);
    });
  });

  describe.skip('FO : Check modified cart rule', async () => {
    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBackFO', baseContext);

      page = await boCartRulesPage.changePage(browserContext, 1);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValueModified', baseContext);

      await foClassicCartPage.reloadPage(page);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountAmount = parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10);

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10 - totalDiscountAmount,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent
        + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded
        + totalShipping
        + totalDiscountAmount;

      const discountValue1 = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(discountValue1).to.equal(`-€${cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded.toFixed(2)}`);

      const discountValue2 = await foClassicCartPage.getCartRuleValue(page, 2);
      expect(discountValue2).to.equal(`-€${parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10).toFixed(2)}`);

      const discountValue3 = await foClassicCartPage.getCartRuleValue(page, 3);
      expect(discountValue3).to.equal('Free shipping');

      const discountValue4 = await foClassicCartPage.getCartRuleValue(page, 4);
      expect(discountValue4).to.equal(`-€${totalDiscountPercent.toFixed(2)}`);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(totalProducts + totalShipping - totalDiscounts);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check the total (tax incl.)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalTaxInc', baseContext);

      const totalProducts = dataProducts.demo_6.price * 10 + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded;

      const totalShipping = dataCarriers.myCarrier.price;

      const totalDiscountAmount = parseInt(cartRuleTestAmount.discountAmount!.value.toString(), 10);

      const totalDiscountPercent = utilsCore.percentage(
        dataProducts.demo_6.price * 10 - totalDiscountAmount,
        cartRuleTestPercent.getDiscountPercent(),
      );

      const totalDiscounts = totalDiscountPercent
        + cartRuleTestFreeGift.freeGiftProduct!.priceTaxExcluded
        + totalShipping
        + totalDiscountAmount;

      const orderTotalTaxInc = await foClassicCheckoutOrderConfirmationPage.getOrderTotal(page);
      expect(orderTotalTaxInc).to.equal(`€${(totalProducts + totalShipping - totalDiscounts).toFixed(2)}`);
    });
  });

  describe('POST-TEST: FO : Reset address', async () => {
    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPageForReset', baseContext);

      await foClassicCheckoutOrderConfirmationPage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to the "Your addresses" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageForReset', baseContext);

      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesPage.pageTitle);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPageForReset', baseContext);

      const addressPosition = await foClassicMyAddressesPage.getAddressPosition(page, dataAddresses.address_2.alias);
      await foClassicMyAddressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await foClassicMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesCreatePage.updateFormTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddressForReset', baseContext);

      const textResult = await foClassicMyAddressesCreatePage.setAddress(page, dataAddresses.address_2);
      expect(textResult).to.equal(foClassicMyAddressesPage.updateAddressSuccessfulMessage);
    });
  });

  describe('POST-TEST: BO : Reset payment preferences ', async () => {
    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBackBOReset', baseContext);

      page = await boCartRulesPage.changePage(browserContext, 0);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
    });

    it('should go to \'Payment > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPageReset', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.paymentParentLink,
        boDashboardPage.preferencesLink,
      );
      await boPaymentPreferencesPage.closeSfToolBar(page);

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });

    it(`should uncheck the ${dataCountries.unitedStates.name} country for '${dataPaymentMethods.wirePayment.moduleName}'`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableReset', baseContext);

        const result = await boPaymentPreferencesPage.setCountryRestriction(
          page,
          dataCountries.unitedStates.id,
          dataPaymentMethods.wirePayment.moduleName,
          false,
        );
        expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
      });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(cartRuleTestAmount.name, `${baseContext}_postTest_0`);
  deleteCartRuleTest(cartRuleTestFreeGift.name, `${baseContext}_postTest_1`);
  deleteCartRuleTest(cartRuleTestFreeShipping.name, `${baseContext}_postTest_2`);
  deleteCartRuleTest(cartRuleTestPercent.name, `${baseContext}_postTest_3`);
});
